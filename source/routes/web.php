<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesUploadController;

Route::get('/', function () {
    return view('index');
});

Route::post('/upload', [FilesUploadController::class, 'upload']);
