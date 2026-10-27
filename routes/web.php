<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscoveryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LiveEventController;
use App\Http\Controllers\MyGigController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaveLiveEventController;
use App\Http\Controllers\SearchSuggestionController;
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

// Autocomplete suggestions from saved upcoming events.
Route::get(
    '/search-suggestions',
    [SearchSuggestionController::class, 'index']
)
    ->middleware('throttle:60,1')
    ->name('search-suggestions.index');

// Browse events stored in GigPulse.
Route::get('/saved-events', [EventController::class, 'index'])
    ->name('saved-events.index');

// Individual saved event page.
Route::get('/events/{event}', [EventController::class, 'show'])
    ->whereNumber('event')
    ->name('events.show');

// Fetch the latest event comments.
Route::get(
    '/events/{event}/comments',
    [CommentController::class, 'index']
)
    ->whereNumber('event')
    ->middleware('throttle:60,1')
    ->name('comments.index');

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
    // Personal gig diary.
    Route::get('/my-gigs', [MyGigController::class, 'index'])
        ->name('my-gigs.index');

    // Sign out.
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Save or update attendance status.
    Route::post(
        '/events/{event}/attendance',
        [AttendanceController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:30,1')
        ->name('attendance.store');

    // Bulk import Ticketmaster listings.
    Route::post('/discover', [DiscoveryController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('discovery.store');

    // Save one selected Ticketmaster event.
    Route::post(
        '/live-events/save',
        [SaveLiveEventController::class, 'store']
    )
        ->middleware('throttle:10,1')
        ->name('live-events.save');

    // Upload or replace a private ticket/concert photo.
    Route::post(
        '/events/{event}/photo',
        [TicketPhotoController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:10,1')
        ->name('ticket-photos.store');

    // Display a photo after checking ownership.
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

    // Delete your own review.
    Route::delete(
        '/reviews/{review}',
        [ReviewController::class, 'destroy']
    )
        ->whereNumber('review')
        ->middleware('throttle:10,1')
        ->name('reviews.destroy');

    // Post a comment.
    Route::post(
        '/events/{event}/comments',
        [CommentController::class, 'store']
    )
        ->whereNumber('event')
        ->middleware('throttle:comment-posts')
        ->name('comments.store');

    // Edit your own comment.
    Route::patch(
        '/comments/{comment}',
        [CommentController::class, 'update']
    )
        ->whereNumber('comment')
        ->middleware('throttle:comment-posts')
        ->name('comments.update');

    // Delete your own comment.
    Route::delete(
        '/comments/{comment}',
        [CommentController::class, 'destroy']
    )
        ->whereNumber('comment')
        ->middleware('throttle:comment-posts')
        ->name('comments.destroy');
});