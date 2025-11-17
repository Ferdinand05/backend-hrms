<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {


        // ringkasan absensi harian
        $dateNow = Carbon::now('Asia/Jakarta')->format('Y-m-d'); //date
        $employeeHasAttendToday = Attendance::whereDate('date', $dateNow)
            ->whereNotNull('clock_in')
            ->count();
        $employeeLateToday = Attendance::whereDate('date', $dateNow)
            ->where('status', 'late')
            ->count();

        if ($request->month) {
            $currentDate = Carbon::parse($request->month);
        } else {
            $currentDate = Carbon::now('Asia/Jakarta');
        }


        // Ambil total leave per bulan (semua status)
        $totalLeaves = Leave::select(
            DB::raw('MONTH(start_date) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('start_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Ambil total leave dengan status "rejected" per bulan
        $rejectedLeaves = Leave::select(
            DB::raw('MONTH(start_date) as month'),
            DB::raw('COUNT(*) as rejected')
        )
            ->whereYear('start_date', now()->year)
            ->where('status', 'rejected')
            ->groupBy('month')
            ->pluck('rejected', 'month');

        // Nama bulan
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Gabungkan data ke bentuk array untuk chart
        $dataChart = collect($months)->map(function ($month, $index) use ($totalLeaves, $rejectedLeaves) {
            return [
                'name' => $month,
                'total' => $totalLeaves[$index + 1] ?? 0,
                'rejected' => $rejectedLeaves[$index + 1] ?? 0,
            ];
        });


        $data = [
            'totalEmployee' => Employee::count(),
            'totalEmployeeActive' => Employee::where('status', 'active')->count(),
            'totalLeaveThisMonth' => Leave::whereMonth('created_at', $currentDate)->count(),
            'totalLeave' => Leave::count(),
            'totalLeavePending' => Leave::where('status', 'pending')->count(),
            'totalUsers' => User::count(),
            'totalThisMonthAttendance' => Attendance::whereMonth('created_at', $currentDate)->count(),
            'totalAttendanceLateThisMonth' => Attendance::whereMonth('created_at', $currentDate)->where('status', 'late')->count(),
            'latestPendingLeaves' => Leave::with('employee')->where('status', 'pending')->latest()->get(),
            'dataChart' => $dataChart,
            'employeeHasAttendToday' => $employeeHasAttendToday,
            'employeeLateToday' => $employeeLateToday
        ];


        return response()->json([
            'data' => $data,
            'message' => 'Dashboard data fetched successfully.',
            'status' => '200'
        ], 200);
    }
}
