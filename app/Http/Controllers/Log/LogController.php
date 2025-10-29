<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'activity' => Activity::all(),
            'message' => 'Fetched all activity.',
            'status' => '200'
        ], 200);
    }
}
