<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'employees' => EmployeeResource::collection(Employee::with('department')->get()),
            'message' => 'Employee list fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'full_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'gender' => 'required|in:female,male,other',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string|max:500',
            'phone' => 'required|string|max:20',
            'hire_date' => 'required|date',
            'position' => 'nullable|string|max:100',
        ]);

        $hire_date = Carbon::parse($request->hire_date);
        $employee_code = $hire_date->format('ymd') . random_int(100, 999);

        if ($request->hasFile('avatar')) {
            $avatar =  $request->file('avatar')->store('avatars', 'public');
        } else {
            $avatar = null;
        }

        $employee = Employee::create([
            'department_id' => $request->department_id,
            'employee_code' => $employee_code,
            'full_name' => $request->full_name,
            'avatar' => $avatar,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'position' => $request->position,
            'status' => 'active',
        ]);

        return response()->json([
            'employee' => new EmployeeResource($employee),
            'message' => 'Employee created successfully',
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json([
            'employee' => new EmployeeResource($employee->load('department')),
            'message' => 'Employee details fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'full_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'gender' => 'required|in:female,male,other',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string|max:500',
            'phone' => 'required|string|max:20',
            'hire_date' => 'required|date',
            'position' => 'nullable|string|max:100',
        ]);



        $oldAvatar = $employee->avatar;
        if ($request->hasFile('avatar')) {

            // jika avatar sudah ada namun ada file upload
            if ($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $avatar =  $request->file('avatar')->store('avatars', 'public');
        } else {
            $avatar = $employee->avatar;
        }

        $employee->update([
            'department_id' => $request->department_id,
            'full_name' => $request->full_name,
            'avatar' => $avatar,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'position' => $request->position,
        ]);

        return response()->json([
            'employee' => new EmployeeResource($employee->fresh('department')),
            'message' => 'Employee updated successfully',
            'status' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json([
            'message' => 'Employee deleted successfully',
            'status' => 200,
        ]);
    }


    public function selectionSetActive(Request $request)
    {
        $Ids = $request->selectionId;

        $employee = Employee::whereIn('id', $Ids)
            ->update([
                'status' => 'active'
            ]);

        if ($employee >= 1) {
            return response()->json([
                'message' => "{$employee} Data selection updated to active!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update all selection'
            ], 400);
        }
    }

    public function selectionSetInactive(Request $request)
    {
        $Ids = $request->selectionId;

        $employee = Employee::whereIn('id', $Ids)
            ->update([
                'status' => 'inactive'
            ]);

        if ($employee >= 1) {
            return response()->json([
                'message' => "{$employee} Data selection updated to Inactive!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update all selection'
            ], 400);
        }
    }

    public function bulkDelete(Request $request)
    {

        $Ids = $request->selectionId;

        $employee = Employee::whereIn('id', $Ids)->delete();



        if ($employee >= 1) {
            return response()->json([
                'message' => "{$employee} Data Deleted!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to delete'
            ], 400);
        }
    }
}
