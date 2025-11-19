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
        $cacheKey = "office:{$id}";
        $ttl = 60 * 60; // 1 hour in seconds

        $officeData = \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, function () use ($id) {
            $office = Office::find($id);
            if (!$office) {
                return null;
            }
            return $office->makeHidden(['updated_at', 'created_at'])->toArray();
        });

        if (!$officeData) {
            return response()->json([
                'office' => null,
                'message' => 'Office not found.',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'office' => $officeData,
            'message' => 'Office data fetched successfully!',
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
