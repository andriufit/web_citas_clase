<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalCenterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppointmentController::class, 'create'])
    ->name('appointments.create');
Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [AppointmentController::class, 'index'])->name('dashboard');

    Route::resource('centers', MedicalCenterController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
