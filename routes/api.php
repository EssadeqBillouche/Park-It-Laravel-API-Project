<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReservationController;

// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reservation Routes
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index']);
        Route::get('/{id}', [ReservationController::class, 'show']);
        Route::post('/', [ReservationController::class, 'store']);
        Route::put('/{id}', [ReservationController::class, 'update']);
        Route::delete('/{id}', [ReservationController::class, 'destroy']);
    });

    // Admin-only Routes
    Route::middleware('Admin')->group(function () {
        // Region Routes
        Route::prefix('regions')->group(function () {
            Route::get('/', [RegionController::class, 'index']);
            Route::get('/{id}', [RegionController::class, 'show']);
            Route::post('/', [RegionController::class, 'store']);
            Route::put('/{region}', [RegionController::class, 'update']);
            Route::delete('/{id}', [RegionController::class, 'destroy']);
        });

        // Parking Routes
        Route::prefix('parkings')->group(function () {
            Route::get('/', [ParkingController::class, 'index']);
            Route::get('/{id}', [ParkingController::class, 'show']);
            Route::post('/', [ParkingController::class, 'store']);
            Route::put('/{id}', [ParkingController::class, 'update']);
            Route::delete('/{id}', [ParkingController::class, 'destroy']);
        });

        // Position Routes
        Route::prefix('positions')->group(function () {
            Route::get('/', [PositionController::class, 'index']);
            Route::get('/{id}', [PositionController::class, 'show']);
            Route::post('/', [PositionController::class, 'store']);
            Route::put('/{id}', [PositionController::class, 'update']);
            Route::delete('/{id}', [PositionController::class, 'destroy']);
        });
    });
});

// Public Statistics Routes
Route::prefix('parkings/stats')->group(function () {
    Route::get('/overview', [ParkingController::class, 'overviewStats']);
    Route::get('/availability', [ParkingController::class, 'availabilityStats']);
});

Route::get('/reservations/stats', [ReservationController::class, 'reservationStats']);
