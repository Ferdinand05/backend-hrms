<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    // for user admin
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('/departments', \App\Http\Controllers\DepartmentController::class);
    Route::apiResource('/employees', \App\Http\Controllers\EmployeeController::class);
    Route::post('/employees/set-active', [EmployeeController::class, 'selectionSetActive']);
    Route::post('/employees/set-inactive', [EmployeeController::class, 'selectionSetInactive']);
    Route::post('/employees/bulk-delete', [EmployeeController::class, 'bulkDelete']);
    Route::apiResource('/attendances', AttendanceController::class);


    // for user employee
    // user store attendance
    Route::post('/attendances/user/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendances/user/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendances/get/today-attendance', [AttendanceController::class, 'getTodayAttendance']);

    // offices
    Route::apiResource('/offices', OfficeController::class);
});


require __DIR__ . '/auth-api.php';
