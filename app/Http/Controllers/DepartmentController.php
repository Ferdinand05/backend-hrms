<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $departments = Cache::remember('departments_list', 600, function () {
            return DepartmentResource::collection(
                Department::with(['employees'])
                    ->get()
            );
        });


        return response()->json(
            [
                'departments' => $departments,
                'message' => 'Department list fetched successfully',
                'status' => 200,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'department' => new DepartmentResource($department),
            'message' => 'Department created successfully',
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return response()->json([
            'department' => DepartmentResource::make($department),
            'message' => 'Department fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'department' => new DepartmentResource($department),
            'message' => 'Department updated successfully',
            'status' => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully',
            'status' => 200,
        ]);
    }
}
