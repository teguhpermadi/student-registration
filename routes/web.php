<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{id}/download', [PrintController::class, 'download'])->name('download');
