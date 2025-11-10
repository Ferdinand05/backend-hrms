<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;


class LogController extends Controller
{
    public function index(Request $request)
    {

        $activities = Activity::latest()->get()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'event' => $activity->event,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'causer_type' => $activity->causer_type,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at->format('d M, Y H:i:s'),
            ];
        });

        $eventCreated = Activity::where('event', 'created')->count();
        $eventUpdated = Activity::where('event', 'updated')->count();
        $eventDeleted = Activity::where('event', 'deleted')->count();


        return response()->json([
            'logs' => $activities,
            'event' => [
                'eventCreated' => $eventCreated,
                'eventUpdated' => $eventUpdated,
                'eventDeleted' => $eventDeleted,
            ],
            'message' => 'Fetched all log activity.',
            'status' => '200'
        ], 200);
    }
}
