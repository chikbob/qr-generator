<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Http\Request;
use Inertia\Inertia;
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


    // 🟣 Обробка сканування динамічного QR
    public function redirect($slug, Request $request)
    {
        $qrCode = QrCode::where('slug', $slug)->firstOrFail();

        if ($qrCode->is_dynamic) {
            QrScan::create([
                'qr_code_id' => $qrCode->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
            ]);

            // увеличиваем счетчик просмотров
            $qrCode->increment('scan_count');
        }

        return redirect()->away($qrCode->content);
    }
}

