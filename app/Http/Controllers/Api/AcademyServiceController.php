<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyService;
use Illuminate\Http\Request;

class AcademyServiceController extends Controller
{
    public function index($academy_id)
{

return AcademyService::where('academy_id',$academy_id)->get();

}

public function vendorservice()
{
    $vendor = auth()->user()->vendor;

    $plans = AcademyService::whereHas('academy', function($q) use ($vendor){
        $q->where('vendor_id', $vendor->id);
    })->get();

    return response()->json($plans);
}




private function allowedServices()
{
    return [
        'football' => [
            'extra_ball',
            'assistant_coach',
            'private_training'
        ],
        'swimming' => [
            'extra_session',
            'swimming_equipment',
            'private_coach'
        ],
        'karate' => [
            'extra_belt',
            'protection_gear',
            'private_session'
        ],
        'gym' => [
            'extra_pt_session',
            'sauna',
            'special_tools'
        ]
    ];
}





public function store(Request $request)
{
    $data = $request->validate([
        'academy_id' => 'required|exists:academies,id',
        'name' => 'required|string',
        'price' => 'required|numeric',
        'duration' => 'required|integer',
        'max_number' => 'required|integer'
    ]);

    $vendor = auth()->user()->vendor;
    $academy = Academy::findOrFail($data['academy_id']);

    if ($academy->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

     $allowed = $this->allowedServices();
    $academyType = $academy->type;  
 

    $service = AcademyService::create([
        'academy_id' => $data['academy_id'],
        'name' => $data['name'],
        'price' => $data['price'],
        'duration' => $data['duration'],
        'max_number' => $data['max_number'],
        'is_active' => true
    ]);

    return response()->json($service);
}


public function update(Request $request,$id)
{

    $vendor = auth()->user()->vendor;

$service = AcademyService::findOrFail($id);

     $academy = $service->academy;

    if ($academy->vendor_id != $vendor->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }


$service->update($request->all());
return response()->json($service);}




public function destroy($id)
{


    $vendor = auth()->user()->vendor;

$service = AcademyService::findOrFail($id);

     $academy = $service->academy;

    if ($academy->vendor_id != $vendor->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }
 $service->delete();
return response()->json(['message'=>'deleted']);
}



public function toggle($id)
{
        $vendor = auth()->user()->vendor;

$service = AcademyService::whereHas('academy',function($q) use( $vendor){
    $q->where('vendor_id',$vendor->id);
})->findOrFail($id);
$service->is_active =!$service->is_active; 
$service->save();
return response()->json($service);

}
public function statistics($service_id)
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $service = AcademyService::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->findOrFail($service_id);

    // نستخدم relation الصح
    $usageCount = $service->bookings()->count();

    $totalRevenue = $service->bookings()->sum('total_price');

    return response()->json([
        'service_id' => $service->id,
        'name' => $service->name,
        'usage_count' => $usageCount,
        'total_revenue' => $totalRevenue
    ]);
}


public function mostRequestedServices()
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $services = AcademyService::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })
    ->withCount('bookings')  
    ->orderByDesc('bookings_count')
    ->get();

    return response()->json($services);
}

}
