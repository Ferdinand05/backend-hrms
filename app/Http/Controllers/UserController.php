<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $users = Cache::remember('users_list', 600, function () {
            return UserResource::collection(
                User::with(['employee', 'role'])
                    ->get()
            );
        });



        return response()->json([
            'users' => $users,
            'message' => 'User list fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
        ]);

        $employee = Employee::find($request->employee_id);

        $user = User::create([
            'name' => $employee->full_name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
            'employee_id' => $request->employee_id,
        ]);

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User created successfully',
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $user = User::with(['employee', 'role'])->findOrFail($id);
        return response()->json([
            'user' => UserResource::make($user),
            'message' => 'User fetched successfully',
            'status' => 200,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role_id' => 'sometimes|required|exists:roles,id',
            'employee_id' => 'sometimes|required|exists:employees,id|unique:users,employee_id,' . $id,
        ]);

        $user = User::findOrFail($id);

        if ($request->employee_id) {
            $employee = Employee::find($request->employee_id);
        } else {
            $employee = Employee::find($user->employee_id);
        }

        $user->update([
            'name' => $employee->full_name,
            'email' => $request->email,
            'password' => $request->password ?? $user->password,
            'role_id' => $request->role_id,
            'employee_id' => $request->employee_id ?? $user->employee_id,
        ]);

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User updated successfully',
            'status' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'status' => 200,
        ], 200);
    }
}
