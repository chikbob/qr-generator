<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    protected function authUserArray()
    {
        $user = auth()->user();
        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'plan_id' => $user->plan_id,
            'is_admin' => (bool) $user->is_admin,
        ] : null;
    }

    // 🟢 Список QR-кодів користувача
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 8;
        $search = trim((string) $request->query('search', ''));
        $filter = (string) $request->query('filter', 'all');
        $sort = strtolower((string) $request->query('sort', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = QrCode::where('user_id', $user->id)->withCount('scans');

        // 🧠 Если план — Free, не показываем динамические QR
        if (!$user->plan || $user->plan->name === 'Free') {
            $query->where('is_dynamic', false);
        }

        if ($filter === 'dynamic') {
            $query->where('is_dynamic', true);
        } elseif ($filter === 'static') {
            $query->where('is_dynamic', false);
        }

        if ($search !== '') {
            $query->where('content', 'like', '%' . $search . '%');
        }

        $query->orderBy('created_at', $sort);

        $paginator = $query->paginate($perPage)->withQueryString();
        $codesCollection = $paginator->getCollection();

        // Keep dynamic QR images in sync with current public tunnel URL for currently visible page.
        $currentPublicBaseUrl = $this->resolvePublicBaseUrl($request);
        if ($currentPublicBaseUrl) {
            $this->refreshDynamicImagesForCodes($codesCollection, $currentPublicBaseUrl);
        }

        $codes = $codesCollection->map(fn($code) => [
            'id' => $code->id,
            'content' => $code->content,
            'type' => $code->type,
            'image_path' => $this->assetWithVersion($code->image_path),
            'size' => $code->size,
            'color_dark' => $code->color_dark,
            'color_light' => $code->color_light,
            'is_dynamic' => $code->is_dynamic,
            'redirect_uuid' => $code->redirect_uuid,
            'slug' => $code->slug,
            'dynamic_url' => $code->is_dynamic ? '/r/' . $code->slug : null,
            'dynamic_url_full' => $code->is_dynamic ? $this->buildDynamicUrl($code->slug, $request) : null,
            'scans_count' => $code->scans_count,
            'created_at' => $code->created_at->toDateTimeString(),
        ]);
        $paginator->setCollection($codes);

        return Inertia::render('QrHistory', [
            'codes' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'filter' => $filter,
                'sort' => $sort,
            ],
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'plan_id' => $user->plan_id,
                    'is_admin' => (bool) $user->is_admin,
                    'plan' => $user->plan?->name ?? 'Free',
                ],
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }


    // 🟡 Створення нового QR-коду (звичайного або динамічного)
    public function store(Request $request)
    {
        $user = auth()->user();
        $planName = $user->plan?->name ?? 'Free';
        $isDynamic = $request->boolean('is_dynamic');

        if ($isDynamic && !in_array($planName, ['Pro', 'Enterprise'])) {
            return redirect()->route('history')
                ->with('error', 'flash.qr.dynamic_only_pro');
        }

        $data = $request->validate([
            'type' => 'required|string',
            'content' => 'required|string|max:500',
            'payload' => 'nullable|array',
            'size' => 'integer|min:100|max:800',
            'color_dark' => 'string',
            'color_light' => 'string',
            'is_dynamic' => 'boolean',
        ]);

        $slug = Str::uuid()->toString();
        $qrContent = $data['content'];
        if ($isDynamic) {
            $dynamicUrl = $this->buildDynamicUrl($slug, $request);
            if ($dynamicUrl === null) {
                return redirect()->route('history')
                    ->with('error', 'Не найден публичный URL для динамического QR. Запустите quick tunnel и повторите.');
            }
            $qrContent = $dynamicUrl;
        }

        $folder = public_path('qr_codes');
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = 'qr-' . time() . '.png';
        $path = 'qr_codes/' . $fileName;

        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->encoding('UTF-8')
            ->size($data['size'])
            ->color(...sscanf($data['color_dark'], "#%02x%02x%02x"))
            ->backgroundColor(...sscanf($data['color_light'], "#%02x%02x%02x"))
            ->generate($qrContent, public_path($path));

        QrCode::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'content' => $data['content'],
            'payload' => $data['payload'] ?? null,
            'image_path' => $path,
            'size' => $data['size'],
            'color_dark' => $data['color_dark'],
            'color_light' => $data['color_light'],
            'is_dynamic' => $isDynamic,
            'slug' => $isDynamic ? $slug : null,
        ]);

        return redirect()->route('history')->with('success', 'flash.qr.saved');
    }

    // 🔴 Видалення QR-коду
    public function destroy($id)
    {
        $qrCode = QrCode::find($id);

        if (!$qrCode) {
            return redirect()->route('history')->with('error', 'flash.qr.not_found');
        }

        // Удаляем файл, если существует
        $filePath = public_path($qrCode->image_path);
        if (!empty($qrCode->image_path) && file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        $qrCode->delete();

        // ✅ Просто редиректим обратно на /history с сообщением
        return redirect()->route('history')->with('success', 'flash.qr.deleted');
    }


    public function redirect($slug, Request $request)
    {
        $qrCode = QrCode::where('slug', $slug)->firstOrFail();

        if ($qrCode->is_dynamic) {
            $ip = $this->getClientIp($request);
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());
            $referer = $request->headers->get('referer');

            // 🔹 Геолокація — без заглушки
            $country = 'Невідомо';
            $city = 'Невідомо';

            try {
                $geo = Http::get("https://ipinfo.io/{$ip}/json")->json();
                $country = $geo['country'] ?? 'Невідомо';
                $city = $geo['city'] ?? 'Невідомо';

                if (!empty($geo['country_name'])) {
                    $country = $geo['country_name'];
                    $city = $geo['city'] ?? 'Невідомо';
                }
            } catch (\Exception $e) {
                // Можна залогувати помилку
                \Log::warning('Geo API failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            QrScan::create([
                'qr_code_id' => $qrCode->id,
                'ip' => $ip,
                'country' => $country,
                'city' => $city,
                'user_agent' => $request->userAgent(),
                'device' => $agent->device() ?: 'Невідомо',
                'browser' => $agent->browser() ?: 'Невідомо',
                'referer' => $referer,
            ]);
        }

        $target = $this->normalizeRedirectTarget($qrCode->content);

        if ($this->shouldRedirectAway($target)) {
            return redirect()->away($target);
        }

        return response($target, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function analytics($id)
    {
        $qrCode = QrCode::with('scans')->withCount('scans')->findOrFail($id);

        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        $scans = $qrCode->scans->map(fn($scan) => [
            'id' => $scan->id,
            'ip' => $scan->ip,
            'country' => $scan->country,
            'city' => $scan->city,
            'location_source' => $this->resolveLocationSource($scan->ip, $scan->country, $scan->city),
            'browser' => $scan->browser,
            'device' => $scan->device,
            'referer' => $scan->referer,
            'created_at' => $scan->created_at->toDateTimeString(),
        ]);

        return Inertia::render('QrAnalytics', [
            'qrCode' => [
                'id' => $qrCode->id,
                'content' => $qrCode->content,
                'image_path' => $this->assetWithVersion($qrCode->image_path),
                'scans_count' => $qrCode->scans_count,
                'created_at' => $qrCode->created_at->toDateTimeString(),
            ],
            'scans' => $scans,
        ]);
    }

    public function deleteAll()
    {
        $user = auth()->user();

        QrCode::where('user_id', $user->id)->delete();

        return back()->with('success', 'flash.qr.deleted_all');
    }

    protected function buildDynamicUrl(string $slug, ?Request $request = null): ?string
    {
        $baseUrl = $this->resolvePublicBaseUrl($request);
        if (!$baseUrl) {
            return null;
        }

        return rtrim($baseUrl, '/') . '/r/' . $slug;
    }

    protected function resolvePublicBaseUrl(?Request $request = null): ?string
    {
        $candidates = [];

        $publicUrl = (string) config('app.public_url');
        if ($publicUrl !== '') {
            $candidates[] = $publicUrl;
        }

        if ($request) {
            $host = $request->getHost();
            if (!empty($host)) {
                $forwardedProto = (string) $request->header('X-Forwarded-Proto');
                $scheme = $forwardedProto !== '' ? trim(explode(',', $forwardedProto)[0]) : $request->getScheme();
                if ($scheme === '') {
                    $scheme = 'https';
                }
                if ($scheme === 'http' && str_contains($host, 'trycloudflare.com')) {
                    $scheme = 'https';
                }
                $candidates[] = $scheme . '://' . $host;
            }
        }

        $appUrl = (string) config('app.url');
        if ($appUrl !== '') {
            $candidates[] = $appUrl;
        }

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizePublicBaseUrl($candidate);
            if ($normalized !== null && !$this->isLocalHostUrl($normalized)) {
                return $normalized;
            }
        }

        return null;
    }

    protected function normalizePublicBaseUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return rtrim($url, '/');
    }

    protected function isLocalHostUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return true;
        }

        return in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true);
    }

    protected function refreshDynamicImagesForCodes(Collection $codes, string $baseUrl): void
    {
        $dynamicCodes = $codes->filter(fn($code) => $code->is_dynamic && !empty($code->slug) && !empty($code->image_path));
        if ($dynamicCodes->isEmpty()) {
            return;
        }

        foreach ($dynamicCodes as $code) {
            $fullPath = public_path($code->image_path);
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $dark = $this->parseHexColor((string) ($code->color_dark ?? '#000000'), [0, 0, 0]);
            $light = $this->parseHexColor((string) ($code->color_light ?? '#ffffff'), [255, 255, 255]);
            $size = (int) ($code->size ?: 300);
            $qrContent = rtrim($baseUrl, '/') . '/r/' . $code->slug;

            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->encoding('UTF-8')
                ->size($size)
                ->color(...$dark)
                ->backgroundColor(...$light)
                ->generate($qrContent, $fullPath);
        }
    }

    /**
     * @param array<int, int> $fallback
     * @return array<int, int>
     */
    protected function parseHexColor(string $hex, array $fallback): array
    {
        $hex = trim($hex);
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
            return $fallback;
        }

        $parts = sscanf($hex, '#%02x%02x%02x');
        if (!is_array($parts) || count($parts) !== 3) {
            return $fallback;
        }

        return array_map(fn($v) => (int) $v, $parts);
    }

    protected function getClientIp(Request $request): string
    {
        $candidates = [
            $request->header('CF-Connecting-IP'),
            $request->header('True-Client-IP'),
        ];

        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            $candidates[] = trim(explode(',', $forwarded)[0]);
        }

        $candidates[] = $request->ip();

        foreach ($candidates as $ip) {
            if (!empty($ip)) {
                return $ip;
            }
        }

        return $request->ip();
    }

    protected function resolveLocationSource(?string $ip, ?string $country, ?string $city): string
    {
        if ($this->isPrivateOrReservedIp($ip)) {
            return 'local_proxy';
        }

        if ($this->isUnknownLocationValue($country) && $this->isUnknownLocationValue($city)) {
            return 'unknown';
        }

        return 'geo';
    }

    protected function isPrivateOrReservedIp(?string $ip): bool
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    protected function isUnknownLocationValue(?string $value): bool
    {
        $normalized = trim((string) $value);

        return $normalized === '' || in_array($normalized, ['Невідомо', 'Неизвестно', 'Unknown', 'unknown', '—', '-'], true);
    }

    protected function shouldRedirectAway(string $content): bool
    {
        $scheme = parse_url($content, PHP_URL_SCHEME);
        if (!$scheme) {
            return false;
        }

        return in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel', 'sms', 'geo'], true);
    }

    protected function normalizeRedirectTarget(string $content): string
    {
        $normalized = trim($content);

        if ($normalized === '') {
            return $content;
        }

        $scheme = parse_url($normalized, PHP_URL_SCHEME);
        if (!empty($scheme)) {
            return $normalized;
        }

        if (preg_match('/\s/u', $normalized)) {
            return $normalized;
        }

        $candidate = 'https://' . $normalized;
        if (!filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $normalized;
        }

        $host = parse_url($candidate, PHP_URL_HOST);
        if (empty($host) || !str_contains($host, '.')) {
            return $normalized;
        }

        return $candidate;
    }

    protected function assetWithVersion(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }

        $url = asset($path);
        $fullPath = public_path($path);
        $version = @filemtime($fullPath);

        if (!$version) {
            return $url;
        }

        return $url . '?v=' . $version;
    }

}
