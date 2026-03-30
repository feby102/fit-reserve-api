<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StadiumScheduleResource;
use App\Models\Stadium;
use App\Models\StadiumSchedule;
use Illuminate\Http\Request;

class StadiumScheduleController extends Controller
{
    
public function store(Request $request, $stadium_id)
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'day'=>'required',
        'start_time'=>'required',
        'end_time'=>'required'
    ]);

    $stadium = Stadium::where('vendor_id', $vendor->id)
        ->findOrFail($stadium_id);

    $data['stadium_id'] = $stadium_id;

    $schedule = StadiumSchedule::create($data);

    return response()->json($schedule);
}

public function index($stadium_id)
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $stadium = Stadium::where('vendor_id', $vendor->id)
        ->findOrFail($stadium_id);

     return response()->json([
        'stadium' => [
            'id' => $stadium->id,
            'name' => $stadium->name,
            'description' => $stadium->description,
            'city' => $stadium->city,
            'address' => $stadium->address,
            'price_per_hour' => $stadium->price_per_hour,
         ],
        'schedules' => $stadium->schedules  
    ]);
}

// public
public function publicIndex($stadium_id)
{
    $stadium = Stadium::findOrFail($stadium_id);

    return response()->json([
        'stadium' => [
             'name' => $stadium->name,
            'description' => $stadium->description,
            'city' => $stadium->city,
            'address' => $stadium->address,
            'price_per_hour' => $stadium->price_per_hour,
         ],
        'schedules' => $stadium->schedules
    ]);
}
}
