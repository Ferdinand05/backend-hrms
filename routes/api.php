<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Profile\ProfileUserController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    //# for user admin
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('/departments', \App\Http\Controllers\DepartmentController::class);
    // employees
    Route::apiResource('/employees', \App\Http\Controllers\EmployeeController::class);
    Route::get('/employees-with-salary-without-payroll', [EmployeeController::class, 'employeesWithSalaryWithoutPayroll']);
    Route::get('/employees-without-salary', [EmployeeController::class, 'employeesDoesntHaveSalary']);
    Route::get('/employees-without-user', [EmployeeController::class, 'employeesDoesntHaveUser']);
    Route::post('/employees/set-active', [EmployeeController::class, 'selectionSetActive']);
    Route::post('/employees/set-inactive', [EmployeeController::class, 'selectionSetInactive']);
    Route::post('/employees/bulk-delete', [EmployeeController::class, 'bulkDelete']);
    // attendance
    Route::apiResource('/attendances', AttendanceController::class);
    // leaves
    Route::apiResource('/leaves', LeaveController::class);
    Route::post('/leaves/bulk/approve', [LeaveController::class, 'bulkApprove']);
    Route::post('/leaves/bulk/reject', [LeaveController::class, 'bulkReject']);
    Route::post('/leaves/bulk/delete', [LeaveController::class, 'bulkDelete']);
    // salaries
    Route::apiResource('/salaries', SalaryController::class);
    // payrolls
    Route::apiResource('/payrolls', PayrollController::class);
    Route::put('/payrolls/bulk/paid', [PayrollController::class, 'selectionSetPaid']);
    Route::post('/payrolls/bulk/delete', [PayrollController::class, 'bulkDelete']);
    Route::post('/payrolls/generate-all', [PayrollController::class, 'generateAllEmployeePayroll']);
    // logs
    Route::apiResource('/logs', LogController::class);
    // offices
    Route::apiResource('/offices', OfficeController::class);


    // --------------------------------------------------------------------------------

    //SECTION for user employee
    // user store leaves
    Route::post('/leaves/user/create', [LeaveController::class, 'userStore']);

    // user get payrolls this month
    Route::get('/payrolls/user/this-month', [PayrollController::class, 'getUserPayrollThisMonth']);

    // user store attendance
    Route::post('/attendances/user/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendances/user/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendances/get/today-attendance', [AttendanceController::class, 'getTodayAttendance']);

    //SECTION profile user
    Route::get('user/profile', [ProfileUserController::class, 'getProfile']);
    Route::get('user/profile/leave', [ProfileUserController::class, 'getLeave']);
    Route::get('user/profile/attendance', [ProfileUserController::class, 'getAttendance']);
    Route::get('user/profile/payroll', [ProfileUserController::class, 'getPayroll']);
});


require __DIR__ . '/auth-api.php';
