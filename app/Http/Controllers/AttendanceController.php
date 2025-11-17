<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Attendance;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Attendance::with('employee');

        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        } else if ($start_date) {
            $query->whereDate('date', $start_date);
        }


        if ($request->filter == 'today') {
            $query->whereDate('date', today('Asia/Jakarta')->toDate());
        } else if ($request->filter == 'this_month') {
            $query->whereMonth('date', Carbon::now('Asia/Jakarta')->month);
        }


        $attendances = $query->latest()->get();
        return response()->json([
            'attendances' => AttendanceResource::collection($attendances),
            'message' => 'Attendances data fetched successfully',
            'status' => '200'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'clock_in' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s'],
            'clock_out' => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'status' => ['required'],
            'date' => ['sometimes', 'required', 'date']
        ]);

        $attendance->update($data);

        return response()->json([
            'message' => 'Attendance updated successfully',
            'status' => '200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    public function getTodayAttendance()
    {

        $attendance = Attendance::where('employee_id', Auth::user()->employee_id)
            ->whereDate('date', today('Asia/Jakarta'))
            ->latest()
            ->first();
        return response()
            ->json(
                [
                    'todayAttendance' => AttendanceResource::make($attendance),
                    'message' => 'Today attendance fetched successfully.',
                    'status' => '200'
                ]
            );
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => ['required'],
            'longitude' => ['required'],
            'picture' => ['required', 'image'],
            'accuracy' => ['required']
        ]);


        // office data
        $office = Office::first();
        $start_time = Carbon::parse($office->start_time);

        // setup date and time
        $dateNow = Carbon::now('Asia/Jakarta')->format('Y-m-d'); //date
        $clock_in = Carbon::now('Asia/Jakarta'); //datetime
        // it would be present or late
        $status = $clock_in->greaterThan($start_time) ? 'late' : 'present';

        $checkAttendanceToday = Attendance::whereDate('date', $dateNow)
            ->where('employee_id', Auth::user()->employee_id)
            ->whereNotNull('clock_in')
            ->exists();


        // if the user already check in today
        if ($checkAttendanceToday) {
            return response()->json([
                'message' => 'You have already checked in today!',
                'status' => '400'
            ], 400);
        }


        // picture / selfie data
        $picture = $request->file('picture')->store('attendances', 'public');


        $attendance = Attendance::create([
            'employee_id' => Auth::user()->employee->id,
            'date' => $dateNow,
            'clock_in' => $clock_in,
            'image_path' => $picture,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'status' => $status
        ]);

        return response()->json([
            'attendance' => $attendance,
            'message' => 'Attendance was successfully recorded',
            'status' => '201'
        ], 201);
    }


    public function checkOut(Request $request)
    {


        $attendance = Attendance::where('employee_id', Auth::user()->employee_id)
            ->whereDate('date', today('Asia/Jakarta'))
            ->latest()
            ->first();


        if ($attendance) {
            $attendance->update([
                'clock_out' => Carbon::now('Asia/Jakarta')
            ]);

            return response()->json([
                'attendance' => $attendance,
                'message' => 'Attendance was successfully updated',
                'status' => '201'
            ], 201);
        } else {
            return response()->json([
                'status' => '400',
                'message' => 'Check Out failed!'
            ], 400);
        }
    }
}
