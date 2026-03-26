<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'sendOtp']);
Route::get('/verify-otp', [App\Http\Controllers\AuthController::class, 'showVerifyOtp'])->name('auth.verify-otp');
Route::post('/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOtp']);
Route::get('/profile-setup', [App\Http\Controllers\AuthController::class, 'showProfileSetup'])->name('auth.profile-setup');
Route::post('/profile-setup', [App\Http\Controllers\AuthController::class, 'updateProfile']);
Route::get('/profile/edit', [App\Http\Controllers\AuthController::class, 'showProfileSetup'])->name('profile.edit');
Route::post('/profile/update', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update');
Route::post('/roommates/add-user/{user}', [App\Http\Controllers\RoommateController::class, 'addFromUser'])->name('roommates.add-user');
Route::any('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('roommates')->group(function () {
        Route::get('/', [App\Http\Controllers\RoommateController::class, 'index'])->name('roommates.index');
        Route::post('/store', [App\Http\Controllers\RoommateController::class, 'store'])->name('roommates.store');
    });

    Route::prefix('expenses')->group(function () {
        Route::get('/', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/store', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    });

    Route::prefix('settlements')->group(function () {
        Route::get('/create', [App\Http\Controllers\SettlementController::class, 'create'])->name('settlements.create');
        Route::post('/store', [App\Http\Controllers\SettlementController::class, 'store'])->name('settlements.store');
    });

    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
});
