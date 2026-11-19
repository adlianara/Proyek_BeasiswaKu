<?php

use App\Http\Controllers\Admin\ScholarshipController as AdminScholarshipController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

Route::get('/', [ScholarshipController::class, 'home'])->name('home');
Route::get('/health', static fn (): JsonResponse => response()->json(['status' => 'ok']))->name('health');
Route::get('/pencarian', [ScholarshipController::class, 'search'])->name('scholarships.search');
Route::get('/beasiswa/{scholarship:slug}', [ScholarshipController::class, 'show'])->name('scholarships.show');

// Admin access intentionally lives on a separate, unlinked path and requires an admin role.
Route::prefix('pengelola')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/masuk', [SessionController::class, 'create'])->name('login');
        Route::post('/masuk', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });
    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminScholarshipController::class, 'index'])->name('dashboard');
        Route::post('/beasiswa', [AdminScholarshipController::class, 'store'])->name('scholarships.store');
        Route::put('/beasiswa/{scholarship}', [AdminScholarshipController::class, 'update'])->name('scholarships.update');
        Route::delete('/beasiswa/{scholarship}', [AdminScholarshipController::class, 'destroy'])->name('scholarships.destroy');
        Route::post('/keluar', [SessionController::class, 'destroy'])->name('logout');
    });
});
