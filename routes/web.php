<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscoveryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LiveEventController;
use App\Http\Controllers\MyGigController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaveLiveEventController;
use App\Http\Controllers\TicketPhotoController;
use Illuminate\Support\Facades\Route;

// Main Discover page: worldwide search and Near me.
Route::get('/', [LiveEventController::class, 'index'])
    ->middleware('throttle:30,1')
    ->name('events.index');

// Keep existing live-search links working.
Route::get('/live-events', [LiveEventController::class, 'index'])
    ->middleware('throttle:30,1')
    ->name('live-events.index');

// Browse events already stored in GigPulse.
Route::get('/saved-events', [EventController::class, 'index'])
    ->name('saved-events.index');

// Individual saved event page.
Route::get('/events/{event}', [EventController::class, 'show'])
    ->whereNumber('event')
    ->name('events.show');

// Registration and login.
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');
});

// Pages and actions for signed-in users.
Route::middleware('auth')->group(function () {
    Route::get('/my-gigs', [MyGigController::class, 'index'])
        ->name('my-gigs.index');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::post(
        '/events/{event}/attendance',
        [AttendanceController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:30,1')
        ->name('attendance.store');

    Route::post('/discover', [DiscoveryController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('discovery.store');

    Route::post(
        '/live-events/save',
        [SaveLiveEventController::class, 'store']
    )
        ->middleware('throttle:10,1')
        ->name('live-events.save');

    // Upload or replace a photo.
    Route::post(
        '/events/{event}/photo',
        [TicketPhotoController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:10,1')
        ->name('ticket-photos.store');

    // View a photo after checking ownership.
    Route::get(
        '/ticket-photos/{attendance}',
        [TicketPhotoController::class, 'show']
    )
        ->whereNumber('attendance')
        ->name('ticket-photos.show');

    // Create or update your review.
    Route::post(
        '/events/{event}/reviews',
        [ReviewController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:10,1')
        ->name('reviews.store');
});