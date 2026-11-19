<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalStatusController;
use App\Http\Controllers\AnimalWeightController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmVetController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccinationSchemeController;
use App\Http\Controllers\VetDashboardController;
use App\Http\Controllers\VetEventController;
use Illuminate\Support\Facades\Route;

// Началната страница води към таблото (а то – към входа, ако не си влязъл).
Route::redirect('/', '/dashboard')->name('home');

// Поканата към собственик се отваря и без вход – собственикът може още да няма акаунт.
Route::get('invitations/{token}', [AcceptInvitationController::class, 'show'])->name('invitations.show');
Route::post('invitations/{token}', [AcceptInvitationController::class, 'store'])->name('invitations.accept');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Регистратор (и админ): регистрация на ферми, покани, назначаване на ветеринар.
    // Тези маршрути са преди farms/{farm}, иначе „create“ би се приело за номер на ферма.
    Route::middleware('role:registrar,admin')->group(function () {
        Route::get('farms/create', [FarmController::class, 'create'])->name('farms.create');
        Route::post('farms', [FarmController::class, 'store'])->name('farms.store');
        Route::get('farms/{farm}/edit', [FarmController::class, 'edit'])->name('farms.edit');
        Route::put('farms/{farm}', [FarmController::class, 'update'])->name('farms.update');
        Route::post('farms/{farm}/invitations', [InvitationController::class, 'store'])->name('farms.invitations.store');
        Route::post('farms/{farm}/vets', [FarmVetController::class, 'store'])->name('farms.vets.store');
        Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    });

    Route::get('farms', [FarmController::class, 'index'])->middleware('role:registrar,inspector,admin')->name('farms.index');

    // Ферма, животни, ветеринарни записи и отчети. Кой какво вижда се решава в Policies.
    Route::get('farms/{farm}', [FarmController::class, 'show'])->name('farms.show');
    Route::get('farms/{farm}/animals', [AnimalController::class, 'index'])->name('farms.animals.index');
    Route::get('farms/{farm}/animals/create', [AnimalController::class, 'create'])->name('farms.animals.create');
    Route::post('farms/{farm}/animals', [AnimalController::class, 'store'])->name('farms.animals.store');

    Route::get('animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');
    Route::put('animals/{animal}/status', [AnimalStatusController::class, 'update'])->name('animals.status.update');
    Route::post('animals/{animal}/weights', [AnimalWeightController::class, 'store'])->name('animals.weights.store');

    Route::get('farms/{farm}/vet-events', [VetEventController::class, 'index'])->name('farms.vet-events.index');
    Route::get('farms/{farm}/vet-events/create', [VetEventController::class, 'create'])->name('farms.vet-events.create');
    Route::post('farms/{farm}/vet-events', [VetEventController::class, 'store'])->name('farms.vet-events.store');
    Route::get('vet-events/{vetEvent}/edit', [VetEventController::class, 'edit'])->name('vet-events.edit');
    Route::put('vet-events/{vetEvent}', [VetEventController::class, 'update'])->name('vet-events.update');
    Route::delete('vet-events/{vetEvent}', [VetEventController::class, 'destroy'])->name('vet-events.destroy');

    Route::get('farms/{farm}/reports', [ReportController::class, 'index'])->name('farms.reports');
    Route::get('farms/{farm}/reports/vet-events/{format}', [ReportController::class, 'vetEvents'])
        ->whereIn('format', ['excel', 'pdf'])
        ->name('farms.reports.vet-events');
    Route::get('farms/{farm}/reports/animals/{format}', [ReportController::class, 'animals'])
        ->whereIn('format', ['excel', 'pdf'])
        ->name('farms.reports.animals');

    // Ветеринар
    Route::get('my-day', [VetDashboardController::class, 'index'])->middleware('role:vet')->name('vet.dashboard');

    // Препаратите се виждат от ветеринаря (за справка) и от админа.
    Route::get('medications', [MedicationController::class, 'index'])->middleware('role:vet,admin')->name('medications.index');

    // Инспектор
    Route::middleware('role:inspector')->group(function () {
        Route::get('control', [ControlController::class, 'index'])->name('control');
        Route::get('inspections/create', [InspectionController::class, 'create'])->name('inspections.create');
        Route::post('inspections', [InspectionController::class, 'store'])->name('inspections.store');
        Route::get('reports/region/{format}', [ReportController::class, 'region'])
            ->whereIn('format', ['excel', 'pdf'])
            ->name('reports.region');
    });

    Route::get('inspections', [InspectionController::class, 'index'])->middleware('role:inspector,admin')->name('inspections.index');

    // Администратор: потребители, номенклатури, ваксинационни схеми, журнал.
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

        Route::get('species', [SpeciesController::class, 'index'])->name('species.index');
        Route::post('species', [SpeciesController::class, 'store'])->name('species.store');
        Route::put('species/{species}', [SpeciesController::class, 'update'])->name('species.update');
        Route::post('species/{species}/breeds', [BreedController::class, 'store'])->name('breeds.store');
        Route::put('breeds/{breed}', [BreedController::class, 'update'])->name('breeds.update');

        Route::get('medications/create', [MedicationController::class, 'create'])->name('medications.create');
        Route::post('medications', [MedicationController::class, 'store'])->name('medications.store');
        Route::get('medications/{medication}/edit', [MedicationController::class, 'edit'])->name('medications.edit');
        Route::put('medications/{medication}', [MedicationController::class, 'update'])->name('medications.update');

        Route::get('vaccination-schemes', [VaccinationSchemeController::class, 'index'])->name('vaccination-schemes.index');
        Route::get('vaccination-schemes/create', [VaccinationSchemeController::class, 'create'])->name('vaccination-schemes.create');
        Route::post('vaccination-schemes', [VaccinationSchemeController::class, 'store'])->name('vaccination-schemes.store');
        Route::get('vaccination-schemes/{scheme}/edit', [VaccinationSchemeController::class, 'edit'])->name('vaccination-schemes.edit');
        Route::put('vaccination-schemes/{scheme}', [VaccinationSchemeController::class, 'update'])->name('vaccination-schemes.update');
        Route::delete('vaccination-schemes/{scheme}', [VaccinationSchemeController::class, 'destroy'])->name('vaccination-schemes.destroy');

        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
