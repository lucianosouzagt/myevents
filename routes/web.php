<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\InvitationController;
use App\Http\Controllers\Web\CheckinController;
use App\Http\Controllers\Web\GuestController;
use App\Http\Controllers\Web\BarbecueController;
use App\Http\Controllers\Web\BarbecueSuggestionController;
use App\Http\Controllers\Web\Admin\BarbecueAdminController;
use App\Http\Controllers\Web\Admin\AnalyticsController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\MailTestController;
use App\Http\Controllers\Web\PasswordForceController;
use App\Http\Controllers\Web\Admin\UserManagementController;
use App\Http\Controllers\Web\Admin\AdminLoginController;
use App\Http\Controllers\Web\Admin\AdminTwoFactorController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminUsersController;

// Web Routes (Browser)

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public RSVP
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/rsvp', [InvitationController::class, 'rsvp'])->name('invitations.rsvp.store');
Route::get('/rsvp/{token}/qrcode', [CheckinController::class, 'showQrCode'])->name('invitations.qrcode');
Route::post('/invitations/{token}/qrcode/send', [InvitationController::class, 'sendQrCode'])->name('invitations.qrcode.send');

// Protected Routes
Route::middleware(['auth','inactivity.timeout','force.password.change'])->group(function () {
    // Events CRUD
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

    // Invitations (Send) - Deprecated/Refactored into GuestController? No, keeping for legacy bulk send if needed, but GuestController is better.
    // Let's keep existing bulk send for now but add Guest management routes.
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');

    // Guest Management
    Route::get('/events/{eventId}/guests', [GuestController::class, 'index'])->name('events.guests.index');
    Route::get('/events/{eventId}/guests/create', [GuestController::class, 'create'])->name('events.guests.create');
    Route::post('/events/{eventId}/guests', [GuestController::class, 'store'])->name('events.guests.store');
    Route::get('/events/{eventId}/guests/{invitationId}/edit', [GuestController::class, 'edit'])->name('events.guests.edit');
    Route::put('/events/{eventId}/guests/{invitationId}', [GuestController::class, 'update'])->name('events.guests.update');
    Route::delete('/events/{eventId}/guests/{invitationId}', [GuestController::class, 'destroy'])->name('events.guests.destroy');
    Route::post('/events/{eventId}/guests/{invitationId}/send', [GuestController::class, 'send'])->name('events.guests.send');
    Route::post('/events/{eventId}/guests/{invitationId}/send-qrcode', [GuestController::class, 'sendQrCode'])->name('events.guests.send_qrcode');

    // Checkin (Organizer)
    Route::post('/checkin', [CheckinController::class, 'store'])->name('checkin.store');
    
    // Manual Checkin Interface
    Route::get('/events/{eventId}/checkin', [CheckinController::class, 'index'])->name('events.checkin.index');
    Route::post('/events/{eventId}/checkin/{invitationId}', [CheckinController::class, 'toggle'])->name('events.checkin.toggle');
    Route::get('/events/{eventId}/checkin/report', [CheckinController::class, 'report'])->name('events.checkin.report');
});

// Force password change
Route::middleware('auth')->group(function () {
    Route::get('/password/force', [PasswordForceController::class, 'form'])->name('password.force.form');
    Route::post('/password/force', [PasswordForceController::class, 'update'])->name('password.force.update');
});

// Barbecue Planner (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/churrasco', [BarbecueController::class, 'index'])->name('barbecue.index');
    Route::post('/churrasco/calcular', [BarbecueController::class, 'calculate'])->name('barbecue.calculate');
    Route::get('/churrasco/sugerir', [BarbecueSuggestionController::class, 'create'])->name('barbecue.suggest');
    Route::post('/churrasco/sugerir', [BarbecueSuggestionController::class, 'store'])->name('barbecue.suggest.store');
});

// Admin auth
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'show'])->name('login.form');
    Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
    Route::get('/2fa', [AdminTwoFactorController::class, 'form'])->name('2fa.form');
    Route::post('/2fa', [AdminTwoFactorController::class, 'verify'])->middleware('throttle:10,1')->name('2fa.verify');
});

// Admin area (separate guard)
Route::middleware(['admin.auth','auth:admin','admin.timeout','admin.2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('home');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    // Suggestions moderation
    Route::get('/churrasco/sugestoes', [BarbecueAdminController::class, 'index'])->name('barbecue.suggestions');
    Route::patch('/churrasco/sugestoes/{id}', [BarbecueAdminController::class, 'moderate'])->name('barbecue.moderate');
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.dashboard');
    Route::get('/analytics/export/csv', [AnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');
    // User management (event creators)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/password/reset', [UserManagementController::class, 'resetPassword'])->name('users.password.reset');

    // Administrative users (guard admin)
    Route::get('/admins', [AdminUsersController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminUsersController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminUsersController::class, 'store'])->name('admins.store');
    Route::get('/admins/{user}/edit', [AdminUsersController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{user}', [AdminUsersController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{user}', [AdminUsersController::class, 'destroy'])->name('admins.destroy');
    Route::post('/admins/{user}/reset', [AdminUsersController::class, 'reset'])->name('admins.reset');
});

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Mail test fallback (evita problemas com cache de rotas em ambientes de teste)
Route::post('/api/mail/test', [MailTestController::class, 'send'])->middleware('throttle:5,1');
