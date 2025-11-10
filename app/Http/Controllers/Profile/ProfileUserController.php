<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\PayrollResource;
use App\Http\Resources\UserResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfileUserController extends Controller
{

    protected $user;
    public function __construct()
    {

        $this->user = Auth::user();
    }

    public function getProfile()
    {
        $user = $this->user
            ->with(['employee', 'employee.department', 'role', 'employee.salary'])
            ->first();

        return response()->json([
            'user' => UserResource::make($user),
            'message' => 'User Profile data fetched successfully.',
            'status' => '200'
        ], 200);
    }


    public function getLeave(Request $request)
    {

        $month = $request->month;
        $leave = Leave::where('employee_id', $this->user->employee_id)
            ->with(['employee', 'approver'])
            ->whereMonth('start_date', Carbon::parse($month)->month)
            ->latest()
            ->get();

        return response()->json([
            'leave' => LeaveResource::collection($leave),
            'message' => 'User Leave data fetched successfully.',
            'status' => '200'
        ], 200);
    }

    public function getAttendance(Request $request)
    {
        $month = $request->month;
        $attendance = Attendance::where('employee_id', $this->user->employee_id)
            ->with('employee')
            ->latest()
            ->whereMonth('date', Carbon::parse($month)->month)
            ->get();

        return response()->json([
            'attendance' => AttendanceResource::collection($attendance),
            'message' => 'User Attendance data fetched successfully.',
            'status' => '200'
        ], 200);
    }

    public function getPayroll(Request $request)
    {
        $year = $request->query('year');
        $payroll = Payroll::where('employee_id', $this->user->employee_id)
            ->where('period', 'LIKE',  $year . '%')
            ->with(['employee', 'salary'])
            ->latest()
            ->get();


        return response()->json([
            'payroll' => PayrollResource::collection($payroll),
            'message' => 'User Payroll data fetched successfully.',
            'status' => '200'
        ], 200);
    }
}
