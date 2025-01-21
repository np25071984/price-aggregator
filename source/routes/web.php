<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordUpdateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', fn () => redirect()->route('get-aggregation') );
    Route::get('/aggregation', [HomeController::class, 'aggregation'])->name('get-aggregation');
    Route::get('/merge', [HomeController::class, 'merge'])->name('get-merge');

    Route::post('/upload', [FilesUploadController::class, 'upload']);

    Route::get('/logout', LogoutController::class)->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', fn () => view('auth.login') )->name('login');
    Route::post('/login', LoginController::class)->name('post-login');

    Route::get('/register', fn () => view('auth.register') )->name('get-register');
    Route::post('/register', RegisterController::class)->name('post-register');

    Route::get('/forgot-password', fn () => view('auth.password-request') )->name('password.request');
    Route::post('/forgot-password', ResetPasswordController::class)->name('password.email');
    Route::get('/reset-password', fn () => view('auth.password-reset') )->name('password.reset');

    Route::post('/reset-password', PasswordUpdateController::class)->name('password.update');
});
