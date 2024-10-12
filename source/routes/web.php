<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesUploadController;
use App\Http\Requests\UploadFilesRequest;

Route::get('/', function () {
    return view('index');
});

Route::post('/upload', [FilesUploadController::class, 'upload']);
