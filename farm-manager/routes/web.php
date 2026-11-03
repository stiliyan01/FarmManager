<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalStatusController;
use App\Http\Controllers\AnimalWeightController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FarmController;
use Illuminate\Support\Facades\Route;

// Началната страница води към таблото (а то – към входа, ако не си влязъл).
Route::redirect('/', '/dashboard')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ферма и животни. Кой какво вижда се решава в Policies.
    Route::get('farms/{farm}', [FarmController::class, 'show'])->name('farms.show');
    Route::get('farms/{farm}/animals', [AnimalController::class, 'index'])->name('farms.animals.index');
    Route::get('farms/{farm}/animals/create', [AnimalController::class, 'create'])->name('farms.animals.create');
    Route::post('farms/{farm}/animals', [AnimalController::class, 'store'])->name('farms.animals.store');

    Route::get('animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');
    Route::put('animals/{animal}/status', [AnimalStatusController::class, 'update'])->name('animals.status.update');
    Route::post('animals/{animal}/weights', [AnimalWeightController::class, 'store'])->name('animals.weights.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
