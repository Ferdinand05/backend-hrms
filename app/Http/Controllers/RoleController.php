<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            'roles' => RoleResource::collection($roles),
            'message' => 'Role list fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|unique:roles,role_name',
        ]);

        $role = Role::create([
            'role_name' => $request->role_name,
        ]);

        return response()->json([
            'role' => RoleResource::make($role),
            'message' => 'Role created successfully',
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return response()->json([
            'role' => RoleResource::make($role),
            'message' => 'Role fetched successfully',
            'status' => 200,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|string|unique:roles,role_name,' . $role->id,
        ]);

        $role->update([
            'role_name' => $request->role_name,
        ]);

        return response()->json([
            'role' => RoleResource::make($role),
            'message' => 'Role updated successfully',
            'status' => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if any user is assigned to this role
        $userCount = User::where('role_id', $role->id)->count();
        if ($userCount > 0) {
            return response()->json([
                'message' => 'Cannot delete role assigned to users',
                'status' => 400,
            ], 400);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
            'status' => 200,
        ]);
    }
}
