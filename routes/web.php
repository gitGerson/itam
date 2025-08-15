<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MobileController;

Route::get('/', function () {
    // return to login
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route group middleware for authenticated users
Route::middleware(['auth'])->group(function () {
    // User routes
    Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('users', UserController::class);

    // Mobile routes
    Route::get('/mobile', [MobileController::class, 'index'])->name('mobile.index');
});
