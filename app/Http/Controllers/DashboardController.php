<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {


        if ($request->month) {
            $currentDate = Carbon::parse($request->month);
        } else {
            $currentDate = Carbon::now('Asia/Jakarta');
        }


        $data = [
            'totalEmployee' => Employee::count(),
            'totalEmployeeActive' => Employee::where('status', 'active')->count(),
            'totalLeaveThisMonth' => Leave::whereMonth('created_at', $currentDate)->count(),
            'totalLeave' => Leave::count(),
            'totalLeavePending' => Leave::where('status', 'pending')->count(),
            'totalUsers' => User::count(),
            'totalThisMonthAttendance' => Attendance::whereMonth('created_at', $currentDate)->count(),
            'totalAttendanceLateThisMonth' => Attendance::whereMonth('created_at', $currentDate)->where('status', 'late')->count()
        ];


        return response()->json([
            'data' => $data,
            'message' => 'Dashboard data fetched successfully.',
            'status' => '200'
        ], 200);
    }
}
