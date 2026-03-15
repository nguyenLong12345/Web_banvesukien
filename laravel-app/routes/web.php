<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\SeatController;
use App\Http\Controllers\Web\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [EventController::class, 'home'])->name('home');
Route::get('/search', [EventController::class, 'search'])->name('search');
Route::get('/api/events/search-suggestions', [EventController::class, 'searchSuggestions'])->name('api.events.suggestions');
Route::get('/events/type/{type}', [EventController::class, 'eventType'])->name('events.type');
Route::get('/events/{event}/payment', [EventController::class, 'payment'])->name('events.payment');

// Chatbot API Route
Route::post('/chatbot/message', [\App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('chatbot.message');

/*
|--------------------------------------------------------------------------
| Auth Routes (User)
|--------------------------------------------------------------------------
*/

Route::get('/login', function() {
    return redirect()->route('home')->with('showLogin', true);
})->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('/register', function() {
    return redirect()->route('home')->with('showRegister', true);
});
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::post('/auth/firebase/verify', [FirebaseAuthController::class, 'verify'])->name('auth.firebase.verify');

Route::get('/password/forgot', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Protected Web Routes (auth required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/events/{event}/select-seats', [SeatController::class, 'selectSeats'])->name('events.select-seats');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/info', [\App\Http\Controllers\Web\ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/password', [\App\Http\Controllers\Web\ProfileController::class, 'changePassword'])->name('profile.update.password');
});

/*
|--------------------------------------------------------------------------
| Booking Flow (auth required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/payment/vnpay/redirect', [PaymentController::class, 'vnpayRedirect'])->name('payment.vnpay.redirect');
});

Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');

Route::post('/subscribe', [\App\Http\Controllers\SubscribeController::class, 'store'])->name('subscribe');

/*
|--------------------------------------------------------------------------
| AJAX Routes (auth required for seat data)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/ajax/events/{event}/seats', [SeatController::class, 'getSeats'])->name('ajax.events.seats');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');



    Route::middleware(\App\Http\Middleware\AdminAuthenticate::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.root');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('events', AdminEventController::class)->except(['show', 'create', 'edit']);
        Route::post('/events/{event}/seats/generate', [AdminEventController::class, 'generateSeats'])->name('events.seats.generate');

        Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/update-ticket', [OrderController::class, 'updateTicket'])->name('orders.update-ticket');

        Route::get('/history', [OrderController::class, 'history'])->name('history');
    });
});
