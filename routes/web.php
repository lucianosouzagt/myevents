<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\InvitationController;
use App\Http\Controllers\Web\CheckinController;
use App\Http\Controllers\Web\GuestController;

// Web Routes (Browser)

// Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('events.my');
    }
    return view('landing');
})->name('home');

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
Route::middleware('auth')->group(function () {
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
