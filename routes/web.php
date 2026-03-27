<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'sendOtp']);
Route::get('/verify-otp', [App\Http\Controllers\AuthController::class, 'showVerifyOtp'])->name('auth.verify-otp');
Route::post('/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOtp']);
Route::middleware(['auth'])->group(function () {
    // These routes only require auth (not group selection)
    Route::get('/profile-setup', [App\Http\Controllers\AuthController::class, 'showProfileSetup'])->name('auth.profile-setup');
    Route::post('/profile-setup', [App\Http\Controllers\AuthController::class, 'updateProfile']);
    Route::get('/profile/edit', [App\Http\Controllers\AuthController::class, 'showProfileSetup'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update');
    Route::any('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    // Group Management
    Route::get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [App\Http\Controllers\GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/join', [App\Http\Controllers\GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/join', [App\Http\Controllers\GroupController::class, 'joinProcess'])->name('groups.join.process');
    Route::post('/groups/{id}/switch', [App\Http\Controllers\GroupController::class, 'switch'])->name('groups.switch');
    Route::post('/groups/clear-data', [App\Http\Controllers\GroupController::class, 'clearData'])->name('groups.clear-data');
    Route::get('/groups/{id}/export-csv', [App\Http\Controllers\GroupController::class, 'exportCsv'])->name('groups.export-csv');
    Route::post('/fcm-token', [App\Http\Controllers\FcmController::class, 'updateToken'])->name('fcm.token');

    // Scoped Routes (Dashboard, Expenses, Roommates) - NOW REQUIRE AUTH + GROUP
    Route::middleware([\App\Http\Middleware\EnsureGroupIsSelected::class])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
        Route::resource('roommates', App\Http\Controllers\RoommateController::class);
        Route::post('/roommates/add-user/{user}', [App\Http\Controllers\RoommateController::class, 'addFromUser'])->name('roommates.add-user');
        Route::post('/settle', [App\Http\Controllers\ExpenseController::class, 'settle'])->name('settle');

        Route::prefix('settlements')->group(function () {
            Route::get('/create', [App\Http\Controllers\SettlementController::class, 'create'])->name('settlements.create');
            Route::post('/store', [App\Http\Controllers\SettlementController::class, 'store'])->name('settlements.store');
        });

        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    });
});

