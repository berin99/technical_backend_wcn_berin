<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/available-classes', [BookingController::class, 'getAvailableClasses']);
Route::post('/book-class', [BookingController::class, 'bookClass']);
Route::delete('/cancel-booking', [BookingController::class, 'cancelBooking']);
Route::get('class-bookings', [BookingController::class, 'getClassBookings']);
