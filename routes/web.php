<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\RoleController;
use App\Http\Controllers\Pages\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout')->middleware(['auth']);

Route::middleware(['auth'])->group(function() {
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
});