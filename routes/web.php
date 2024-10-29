<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{id}/print-preview', [PrintController::class, 'print_preview'])->name('print-preview');
