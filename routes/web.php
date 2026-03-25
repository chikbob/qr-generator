<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PlanController;
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
| Laravel сам применяет middleware "web" ко всем маршрутам из этого файла.
|
*/

/*
|--------------------------------------------------------------------------
| Публичные страницы
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => inertiaWithUser('Home'))->name('home');
Route::get('/scan', fn() => inertiaWithUser('QrScanner'))->name('scan');

// 🟢 только для авторизованных
Route::middleware(['auth'])->group(function () {

    Route::get('/generate', fn() => inertiaWithUser('QrGenerator'))->name('generate');
    Route::get('/history', [QrCodeController::class, 'index'])->name('history');

    Route::get('/profile', [ProfileController::class, 'show'])
        ->middleware(['verified'])
        ->name('profile.show');
    
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}/payment', [PlanController::class, 'payment'])->name('plans.payment');
    Route::post('/plans/{plan}/pay', [PlanController::class, 'pay'])->name('plans.pay');
    Route::post('/plans/subscribe', [PlanController::class, 'subscribe'])->name('plans.subscribe');

    Route::resource('qr', QrCodeController::class)
        ->only(['index', 'store', 'destroy']);

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    Route::get('/qr/{id}/analytics', [QrCodeController::class, 'analytics'])->name('qr.analytics');

    Route::delete('/qr/delete-all', [QrCodeController::class, 'deleteAll'])->name('qr.deleteAll');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/{table}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{table}', [AdminController::class, 'store'])->name('store');
    Route::get('/{table}/{id}/edit', [AdminController::class, 'edit'])->name('edit');
    Route::patch('/{table}/{id}', [AdminController::class, 'update'])->name('update');
    Route::delete('/{table}/{id}', [AdminController::class, 'destroy'])->name('destroy');
    Route::get('/{table}', [AdminController::class, 'table'])->name('table');
});

// 🔗 редирект динамических QR (публичный)
Route::get('/r/{slug}', [QrCodeController::class, 'redirect'])->name('qr.redirect');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('web')
    ->name('logout');

Route::get('/contacts', fn() => inertiaWithUser('Contact'))
    ->middleware(['auth'])
    ->name('contacts');

// 🔐 Breeze/Fortify маршруты
require __DIR__ . '/auth.php';

// 🏥 Health check маршруты
require __DIR__ . '/health.php';
