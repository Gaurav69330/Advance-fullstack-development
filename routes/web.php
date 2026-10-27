<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MyGigController;
use Illuminate\Support\Facades\Route;

// Event discovery pages.
Route::get('/', [EventController::class, 'index'])
    ->name('events.index');

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
});