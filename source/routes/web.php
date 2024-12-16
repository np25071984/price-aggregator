<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesUploadController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('index');;

Route::post('/upload', [FilesUploadController::class, 'upload']);
