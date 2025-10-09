<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');
