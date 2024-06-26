<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\LawsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//this is the endpoint with prefix /api

Route::post('/login', [UsersController::class, 'login']);
Route::post('/register', [UsersController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UsersController::class, 'index']);
    Route::post('/fav', [UsersController::class, 'storeFavDoc']);
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::post('/book', [AppointmentsController::class, 'store']);
    Route::post('/appointments/cancel/{id}', [AppointmentsController::class, 'cancel']);
    Route::post('/reviews', [LawsController::class, 'store']);
    Route::post('/check-slot', [AppointmentsController::class, 'checkSlot']);
    Route::get('/appointments', [AppointmentsController::class, 'index']);
    Route::post('/reschedule', [AppointmentsController::class, 'reschedule']);
    Route::get('reviews/{lawId}', [UsersController::class, 'getReviews']);
    Route::put('/update', [UsersController::class, 'update']);
    Route::post('/update/photo', [UsersController::class, 'uploadPhoto']);
});
