<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesUploadController;
use App\Http\Controllers\HomeController;

Route::get('/', fn () => redirect()->route('get-aggregation') );
Route::get('/aggregation', [HomeController::class, 'aggregation'])->name('get-aggregation');
Route::get('/merge', [HomeController::class, 'merge'])->name('get-merge');

Route::post('/upload', [FilesUploadController::class, 'upload']);
