<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Http\Request;
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
        ] : null;
    }

    // 🟢 Список QR-кодів користувача
    public function index()
    {
        $user = auth()->user();

        $query = QrCode::where('user_id', $user->id)->latest()->withCount('scans');

        // 🧠 Если план — Free, не показываем динамические QR
        if (!$user->plan || $user->plan->name === 'Free') {
            $query->where('is_dynamic', false);
        }

        $codes = $query->get()->map(fn($code) => [
            'id' => $code->id,
            'content' => $code->content,
            'image_path' => asset($code->image_path),
            'size' => $code->size,
            'color_dark' => $code->color_dark,
            'color_light' => $code->color_light,
            'is_dynamic' => $code->is_dynamic,
            'redirect_uuid' => $code->redirect_uuid,
            'slug' => $code->slug,
            'dynamic_url' => $code->is_dynamic ? url('/r/' . $code->slug) : null,
            'scans_count' => $code->scans_count,
            'created_at' => $code->created_at->toDateTimeString(),
        ]);

        return Inertia::render('QrHistory', [
            'codes' => $codes,
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
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
        if ($request->boolean('is_dynamic') && !in_array($planName, ['Pro', 'Enterprise'])) {
            return redirect()->route('history')->with('error', 'Динамічні QR-коди доступні лише для користувачів з планом Pro або Enterprise.');
        }

        $data = $request->validate([
            'content' => 'required|string|max:500',
            'size' => 'integer|min:100|max:800',
            'color_dark' => 'string',
            'color_light' => 'string',
            'is_dynamic' => 'boolean',
        ]);

        $folder = public_path('qr_codes');
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = 'qr-' . time() . '.png';
        $path = 'qr_codes/' . $fileName;
        $slug = \Str::uuid()->toString();

        $finalContent = $data['is_dynamic']
            ? url('/r/' . $slug)
            : $data['content'];

        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->encoding('UTF-8')
            ->size($data['size'])
            ->color(...sscanf($data['color_dark'], "#%02x%02x%02x"))
            ->backgroundColor(...sscanf($data['color_light'], "#%02x%02x%02x"))
            ->generate($finalContent, public_path($path));

        QrCode::create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'image_path' => $path,
            'size' => $data['size'],
            'color_dark' => $data['color_dark'],
            'color_light' => $data['color_light'],
            'is_dynamic' => $data['is_dynamic'] ?? false,
            'slug' => $slug,
            'redirect_uuid' => $data['is_dynamic'] ? $slug : null,
        ]);

        return redirect()->route('history')->with('success', 'QR-код збережено!');
    }

    // 🔴 Видалення QR-коду
    public function destroy($id)
    {
        $qrCode = QrCode::find($id);

        if (!$qrCode) {
            return redirect()->route('history')->with('error', 'QR-код не знайдено');
        }

        // Удаляем файл, если существует
        $filePath = public_path($qrCode->image_path);
        if (!empty($qrCode->image_path) && file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        $qrCode->delete();

        // ✅ Просто редиректим обратно на /history с сообщением
        return redirect()->route('history')->with('success', 'QR-код видалено!');
    }


    public function redirect($slug, Request $request)
    {
        $qrCode = QrCode::where('slug', $slug)->firstOrFail();

        if ($qrCode->is_dynamic) {
            $ip = $request->ip();
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

        return redirect()->away($qrCode->content);
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
            'browser' => $scan->browser,
            'device' => $scan->device,
            'referer' => $scan->referer,
            'created_at' => $scan->created_at->toDateTimeString(),
        ]);

        return Inertia::render('QrAnalytics', [
            'qrCode' => [
                'id' => $qrCode->id,
                'content' => $qrCode->content,
                'image_path' => asset($qrCode->image_path),
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

        return back()->with('success', 'Усі QR-коди видалено успішно!');
    }
}

