<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyPlan;
use Illuminate\Http\Request;

class AcademyPlanController extends Controller
{


 public function index($academy_id)
{

return AcademyPlan::where('academy_id',$academy_id)->get();

}

public function vendorPlans()
{
    $vendor = auth()->user()->vendor;

    $plans = AcademyPlan::whereHas('academy', function($q) use ($vendor){
        $q->where('vendor_id', $vendor->id);
    })->get();

    return response()->json($plans);
}

 public function store(Request $request)
{
    $data = $request->validate([
        'academy_id' => 'required|exists:academies,id',
        'name' => 'required',
        'type' => 'required',
        'price' => 'required|numeric',
        'max_students' => 'required|integer'
    ]);

     $vendor = auth()->user()->vendor;

     $academy = Academy::findOrFail($data['academy_id']);

     if ($academy->vendor_id != $vendor->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $plan = AcademyPlan::create($data);

    return response()->json($plan);
}




public function update(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

    $plan = AcademyPlan::findOrFail($id);

     $academy = $plan->academy;

    if ($academy->vendor_id != $vendor->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $plan->update($request->all());

    return response()->json($plan);
}


public function destroy($id)
{
    $vendor = auth()->user()->vendor;

    $plan = AcademyPlan::findOrFail($id);

    $academy = $plan->academy;

    if ($academy->vendor_id != $vendor->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $plan->delete();

    return response()->json([
        'message' => 'deleted'
    ]);
}


}
