<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->month) {
            $payrolls = Payroll::query()
                ->where('period', $request->month)
                ->with(['employee', 'salary'])
                ->latest()
                ->get();
        } else {
            $payrolls = Payroll::query()
                ->where('period', Carbon::now('Asia/Jakarta')->format('Y-m'))
                ->with(['employee', 'salary'])
                ->latest()
                ->get();
        }


        return response()->json([
            'payrolls' => PayrollResource::collection($payrolls),
            'message' => 'Payrolls data fetched successfully.',
            'status' => '200'
        ]);
    }

    public function getUserPayrollThisMonth()
    {

        $currentMonth = Carbon::now('Asia/Jakarta')->format('Y-m');
        $payroll = Payroll::where('period', $currentMonth)
            ->with(['employee', 'salary'])
            ->where('employee_id', Auth::user()->employee_id)
            ->first();

        if ($payroll) {
            return response()->json([
                'payroll' => PayrollResource::make($payroll),
                'message' => 'Payroll data fetched successfully.',
                'status' => '200'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Payroll data fetched failed.',
                'status' => '400'
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_id' => 'required',
            'base_salary' => 'required|numeric',
            'total_salary' => 'required|numeric',
            'overtime_hours' => 'required|numeric',
            'overtime_pay' => 'required|numeric',
            'deduction' => 'required|numeric',
            'allowance' => 'required|numeric',
            'period' => 'required',
        ]);


        $payrollCheck = Payroll::where('employee_id', $request->employee_id)
            ->where('period', $request->period)
            ->exists();

        if ($payrollCheck) {
            return response()->json([
                'message' => 'Payroll for this employee and period already exists.',
                'status' => 409, // Conflict
            ], 409);
        }

        $payroll = Payroll::create($validated);


        return response()->json([
            'payroll' => PayrollResource::make($payroll),
            'message' => 'Payroll created successfully.',
            'status' => '201'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'total_salary' => 'required|numeric',
            'overtime_hours' => 'required|sometimes|numeric',
            'overtime_pay' => 'required|sometimes|numeric',
            'period' => 'required',
        ]);

        $payroll->update($validated);

        return response()->json([
            'payroll' => PayrollResource::make($payroll),
            'message' => 'Payroll updated successfully.',
            'status' => '200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        //
    }


    public function selectionSetPaid(Request $request)
    {
        $Ids = $request->selectionId;

        $payroll = Payroll::whereIn('id', $Ids)
            ->update([
                'status' => 'paid'
            ]);

        $user = auth()->user();
        activity('payroll_log')
            ->performedOn(new Payroll())
            ->event('updated')
            ->causedBy(auth()->user())
            ->log("{$user->name} Set Paid {$payroll} Payroll");


        if ($payroll >= 1) {
            return response()->json([
                'message' => "{$payroll} Data selection updated to Paid!"
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

        $payroll = Payroll::whereIn('id', $Ids)
            ->delete();


        $user = auth()->user();
        activity('payroll_log')
            ->performedOn(new Payroll())
            ->event('bulk_delete')
            ->causedBy(auth()->user())
            ->log("{$user->name} Bulk Delete {$payroll} Payroll");

        if ($payroll >= 1) {
            return response()->json([
                'message' => "{$payroll} Data selection Deleted!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to delete all selection'
            ], 400);
        }
    }
}
