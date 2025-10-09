<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('/users', UserController::class);
    Route::apiResource('/roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('/departments', \App\Http\Controllers\DepartmentController::class);
    Route::apiResource('/employees', \App\Http\Controllers\EmployeeController::class);
});


require __DIR__ . '/auth-api.php';
