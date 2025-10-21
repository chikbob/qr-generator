<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Все маршруты Inertia и авторизации работают под группой "web",
| чтобы Laravel мог корректно обрабатывать сессии и cookies.
|
*/

/**
 * Вспомогательная функция для рендера Inertia с прокинутым пользователем
 */
if (!function_exists('inertiaWithUser')) {
    function inertiaWithUser(string $component, array $props = [])
    {
        $user = Auth::user();
        return Inertia::render($component, array_merge($props, [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]));
    }
}

// Публичные страницы
Route::middleware(['web'])->group(function () {

    Route::get('/', fn() => inertiaWithUser('Home'))->name('home');
    Route::get('/generate', fn() => inertiaWithUser('QrGenerator'))->name('generate');
    Route::get('/scan', fn() => inertiaWithUser('QrScanner'))->name('scan');

    // Авторизованные маршруты
    Route::middleware(['auth'])->group(function () {
        Route::get('/history', [QrCodeController::class, 'index'])->name('history');
        Route::get('/profile', fn() => inertiaWithUser('Profile'))->name('profile');

        // Контроллер QR-кодов
        Route::resource('qr', QrCodeController::class)
            ->only(['index', 'store', 'destroy']);
    });

    // маршрут для редиректу динамічних QR (без авторизації)
    Route::get('/r/{uuid}', [QrCodeController::class, 'redirect'])->name('qr.redirect');

    // Breeze/Fortify/Jetstream маршруты
    require __DIR__ . '/auth.php';
});

// Тестовые маршруты
Route::middleware(['web'])->group(function () {
    Route::get('/test-user', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });

    Route::get('/test-inertia', fn() => inertiaWithUser('Home'));
});
