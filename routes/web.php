<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\Dashboard\ScanKAController;
use App\Http\Controllers\Pages\Dashboard\ScanPerDinasController;
use App\Http\Controllers\Pages\Dashboard\PeriodeKelilingController;
use App\Http\Controllers\Pages\Dashboard\RerataKelilingController;
use App\Http\Controllers\Pages\RoleController;
use App\Http\Controllers\Pages\UserController;
use App\Http\Controllers\Pages\TrainsController;
use App\Http\Controllers\Pages\ScheduleController;
use App\Http\Controllers\Pages\RangkaianController;
use App\Http\Controllers\Pages\RekapController;
use App\Http\Controllers\Pages\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresentationController;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Route Presentasi (Magic Button)
Route::get('/presentasi-magic', [PresentationController::class, 'index'])->name('presentasi.index');
Route::post('/presentasi-magic/seed', [PresentationController::class, 'generate'])->name('presentasi.seed');
Route::post('/presentasi-magic/reset', [PresentationController::class, 'reset'])->name('presentasi.reset');

// Dashboard Sub-pages (public)
Route::get('/scanKA', [ScanKAController::class, 'index'])->name('dashboard.scanKA');
Route::get('/scanPerDinas', [ScanPerDinasController::class, 'index'])->name('dashboard.scanPerDinas');
Route::get('/periodeKeliling', [PeriodeKelilingController::class, 'index'])->name('dashboard.periodeKeliling');
Route::get('/rerataKeliling', [RerataKelilingController::class, 'index'])->name('dashboard.rerataKeliling');

Route::middleware(['auth'])->group(function() {
    
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === Dashboard API Routes for AJAX Polling ===
    Route::get('/dashboard/api-stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/api-scan-ka', [ScanKAController::class, 'getStats'])->name('dashboard.scanKA.stats');
    Route::get('/dashboard/api-scan-dinas', [ScanPerDinasController::class, 'getStats'])->name('dashboard.scanPerDinas.stats');
    Route::get('/dashboard/api-periode-keliling', [PeriodeKelilingController::class, 'getStats'])->name('dashboard.periodeKeliling.stats');
    Route::get('/dashboard/api-rerata-keliling', [RerataKelilingController::class, 'getStats'])->name('dashboard.rerataKeliling.stats');
    // ===============================================


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
        Route::get('/api-online-status', [UserController::class, 'getOnlineStatus'])->name('user.onlineStatus');
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

    // Rekap Routes
    Route::prefix('rekap')->group(function () {
        Route::get('/', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/export', [RekapController::class, 'export'])->name('rekap.export');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});