<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LawsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AppointmentsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', [LawsController::class, 'index'])->name('dashboard');
    Route::get('/upcoming-appointments', [AppointmentsController::class, 'upcoming'])->name('upcoming.appointments');
    Route::patch('/appointments/{appointment}/update-status', [AppointmentsController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::get('/history', [AppointmentsController::class, 'history'])->name('history');
});
