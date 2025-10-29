<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(string $id)
    {
        $office = Office::first();

        $office->makeHidden(['updated_at', 'created_at']);
        return response()->json([
            'office' => $office,
            'message' => 'Office data fetched succesfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'app_name' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric',
            'max_accuracy' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $office = Office::find($id);
        $office->update($data);

        return response()->json([
            'office' => $office,
            'message' => 'Office updated successfully.',
            'status' => '200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
