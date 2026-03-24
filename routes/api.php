<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\CitaController;

Route::apiResource('users', UserController::class);
Route::apiResource('especialidades', EspecialidadController::class);
Route::apiResource('citas', CitaController::class);
Route::post('appointments', [CitaController::class, 'store']);
