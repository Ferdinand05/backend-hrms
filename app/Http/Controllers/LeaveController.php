<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeaveResource;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Leave::with('employee', 'approver');

        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        } else if ($start_date) {
            $query->whereDate('created_at', $start_date);
        }

        $leaves = $query->latest()->get();


        return response()->json([
            'leaves' => LeaveResource::collection($leaves),
            'message' => 'Leaves data fetched successfully.',
            'status' => '200'
        ]);
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
    public function show(Leave $leave)
    {
        return response()->json([
            'leave' => LeaveResource::make($leave),
            'message' => 'leave fetched succesfully.',
            'status' => '200'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'leave_type' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'reason' => ['required', 'string']
        ]);


        $leave = Leave::findOrFail($id);
        $leave->update($data);

        return response()->json([
            'leave' => LeaveResource::make($leave),
            'message' => 'Leave updated successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leave = Leave::whereId($id)->delete();

        if ($leave) {
            return response()->json([
                'message' => 'Leave deleted',
                'status' => '200'
            ]);
        }
    }


    // # Bulk Action
    public function bulkApprove(Request $request)
    {
        $ids = $request->selection_id;

        $leave = Leave::whereIn('id', $ids)->update([
            'status' => 'approved',
            'approved_by' => Auth::id()
        ]);

        $user = auth()->user();
        activity('leave_log')
            ->performedOn(new Leave())
            ->event('updated')
            ->causedBy(auth()->user())
            ->log("{$user->name} Set Approved {$leave} Leave");

        if ($leave >= 1) {
            return response()->json([
                'message' => "{$leave} Data sselection updated to approved!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update all selection'
            ], 400);
        }
    }

    public function bulkReject(Request $request)
    {
        $ids = $request->selection_id;

        $leave = Leave::whereIn('id', $ids)->update([
            'status' => 'rejected',
            'approved_by' => Auth::id()
        ]);

        $user = auth()->user();
        activity('leave_log')
            ->performedOn(new Leave())
            ->event('updated')
            ->causedBy(auth()->user())
            ->log("{$user->name} Set Rejected {$leave} Leave");

        if ($leave >= 1) {
            return response()->json([
                'message' => "{$leave} Data selection updated to rejected!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update all selection'
            ], 400);
        }
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->selection_id;

        $leave = Leave::whereIn('id', $ids)->delete();


        $user = auth()->user();
        activity('leave_log')
            ->performedOn(new Leave())
            ->event('bulk_delete')
            ->causedBy(auth()->user())
            ->log("{$user->name} Bulk Delete {$leave} Leave");

        if ($leave >= 1) {
            return response()->json([
                'message' => "{$leave} Data selection  deleted!"
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to delete all selection'
            ], 400);
        }
    }



    //# for user
    public function userStore(Request $request)
    {
        $data = $request->validate([
            'leave_type' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
        ]);

        $data['employee_id'] = Auth::user()->employee_id;

        $leave = Leave::create($data);

        return response()->json([
            'leave' => LeaveResource::make($leave),
            'message' => 'Leave request created',
            'status' => '201'
        ], 201);
    }
}
