<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RoommateController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettlementController;
use App\Http\Middleware\EnsureGroupIsSelected;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'sendOtp'])->name('login.send-otp');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('auth.verify-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp.post');
Route::middleware(['auth'])->group(function () {
    // These routes only require auth (not group selection)
    Route::get('/profile-setup', [AuthController::class, 'showProfileSetup'])->name('auth.profile-setup');
    Route::post('/profile-setup', [AuthController::class, 'updateProfile'])->name('auth.profile-setup.post');
    Route::get('/profile/edit', [AuthController::class, 'showProfileSetup'])->name('profile.edit');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

    // Group Management
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/join', [GroupController::class, 'joinProcess'])->name('groups.join.process');
    Route::post('/groups/{id}/switch', [GroupController::class, 'switch'])->name('groups.switch');
    Route::post('/groups/clear-data', [GroupController::class, 'clearData'])->name('groups.clear-data');
    Route::get('/groups/{id}/export-csv', [GroupController::class, 'exportCsv'])->name('groups.export-csv');
    Route::post('/fcm-token', [FcmController::class, 'updateToken'])->name('fcm.token');
    Route::post('/fcm-token/delete', [FcmController::class, 'deleteToken'])->name('fcm.token.delete');

    // Scoped Routes (Dashboard, Expenses, Roommates) - NOW REQUIRE AUTH + GROUP
    Route::middleware([EnsureGroupIsSelected::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('expenses', ExpenseController::class);
        Route::resource('roommates', RoommateController::class);
        Route::post('/roommates/add-user/{user}', [RoommateController::class, 'addFromUser'])->name('roommates.add-user');
        Route::post('/settle', [ExpenseController::class, 'settle'])->name('settle');

        Route::prefix('settlements')->group(function () {
            Route::get('/create', [SettlementController::class, 'create'])->name('settlements.create');
            Route::post('/store', [SettlementController::class, 'store'])->name('settlements.store');
        });

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    });
});
