<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/{id}/download', [PrintController::class, 'download'])->name('download');
Route::get('/{id}/letter', [PrintController::class, 'letter'])->name('letter');
