<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalaryResource;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // jika Redis mendukung tags, gunakan Cache::tags(['salaries']) agar mudah flush
        $salaries = Cache::remember('salaries_list', 600, function () {
            // cache data yang sudah di-transform jadi array (bukan Resource object)
            return SalaryResource::collection(
                Salary::with('employee')->latest()->get()
            );
        });

        return response()->json([
            'salaries' => $salaries,
            'message' => 'Salaries fetched successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:salaries,employee_id',
            'base_salary' => ['required', 'numeric'],
            'allowance' => ['required', 'numeric'],
            'deduction' => ['required', 'numeric'],
            'overtime_rate' => ['required', 'numeric'],
        ]);

        $salary = Salary::create($data);

        return response()->json([
            'salary' => SalaryResource::make($salary),
            'message' => 'Salary added successfully.',
            'status' => '201'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Salary $salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'employee_id' => 'required|sometimes|exists:employees,id|unique:salaries,employee_id,' . $id,
            'base_salary' => ['required', 'sometimes', 'numeric'],
            'allowance' => ['required', 'sometimes', 'numeric'],
            'deduction' => ['required', 'sometimes', 'numeric'],
            'overtime_rate' => ['required', 'sometimes', 'numeric'],
        ]);

        $salary = Salary::findOrFail($id);
        $salary->update($data);


        return response()->json([
            'salary' => $salary,
            'message' => 'Salary updated successfully.',
            'status' => '200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        $salary = Salary::findOrFail($id);


        if ($salary) {
            $salary->delete();

            return response()->json([
                'message' => 'Salary deleted successfully.',
                'status' => '200'
            ]);
        }
    }
}
