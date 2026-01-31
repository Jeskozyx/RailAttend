<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\RoleController;
use App\Http\Controllers\Pages\UserController;
use App\Http\Controllers\Pages\TrainsController;
use App\Http\Controllers\Pages\ScheduleController;
use App\Http\Controllers\Pages\RangkaianController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function() {
    
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('role.index');
        Route::get('/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::put('/{id}/update', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{id}/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('train')->group(function () {
        Route::get('/', [TrainsController::class, 'index'])->name('train.index');
        Route::get('/create', [TrainsController::class, 'create'])->name('train.create');
        Route::post('/store', [TrainsController::class, 'store'])->name('train.store');
        Route::get('/{id}/edit', [TrainsController::class, 'edit'])->name('train.edit');
        Route::put('/{id}/update', [TrainsController::class, 'update'])->name('train.update');
        Route::delete('/{id}/destroy', [TrainsController::class, 'destroy'])->name('train.destroy');
    });

    Route::prefix('schedule')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('schedule.create');
        Route::post('/store', [ScheduleController::class, 'store'])->name('schedule.store');
        Route::get('/{id}/edit', [ScheduleController::class, 'edit'])->name('schedule.edit');
        Route::put('/{id}/update', [ScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/{id}/destroy', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
    });

    Route::post('/rangkaian/store', [RangkaianController::class, 'store'])->name('rangkaian.store');
    Route::post('/rangkaian/reorder', [RangkaianController::class, 'reorder'])->name('rangkaian.reorder');
    Route::post('/rangkaian/regenerate-names/{train_id}', [RangkaianController::class, 'regenerateNames'])->name('rangkaian.regenerate_names');
    Route::delete('/rangkaian/{id}/destroy', [RangkaianController::class, 'destroy'])->name('rangkaian.destroy');
    Route::delete('/rangkaian/{train_id}/destroy-all', [RangkaianController::class, 'destroyAll'])->name('rangkaian.destroy_all');
    Route::get('/rangkaian/cetak/{train_id}', [RangkaianController::class, 'printQr'])->name('rangkaian.print');

    Route::get('/jadwal', [ScheduleController::class, 'jadwalKondektur'])->name('jadwal.view');
    Route::get('/gerbong/{id}', [ScheduleController::class, 'showGerbong'])->name('gerbong.show');
    Route::post('/scan/process', [ScheduleController::class, 'processScan'])->name('kondektur.process_scan');
    Route::get('/verifikasi/{schedule_id}/{rangkaian_id}', [ScheduleController::class, 'verifikasi'])->name('kondektur.verifikasi');

    Route::post('/kondektur/submit-report', [ScheduleController::class, 'submitReport'])->name('kondektur.submit_report');
});