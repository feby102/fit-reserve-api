<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademyPlan;
use App\Models\AcademySubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AcademySubscriptionController extends Controller
{




     public function store(Request $request)
    {
$data=$request->validate(
    ['academy_plan_id'=>'required|exists:academy_plans,id',
    
]);


$user=$request->user();
$plan=AcademyPlan::findOrFail($request->academy_plan_id);

$start = Carbon::today();

$end = Carbon::today()->addDays($plan->duration);

$subscription = AcademySubscription::create([

'user_id'=>$user->id,

'academy_plan_id'=>$plan->id,

'start_date'=>$start,

'end_date'=>$end,

'status'=>'active'

]);


return response()->json([

'message'=>'Subscribed successfully',

'data'=>$subscription

]);

}




public function mySubscriptions(Request $request)
{
 
    return AcademySubscription::where('user_id',$request->user()->id)->with('plan')->get();

}


public function vendorIndex(Request $request)
{
    $vendor = $request->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $subscriptions = AcademySubscription::whereHas('plan.academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })
    ->with(['plan.academy', 'user'])  
    ->latest()
    ->get();

    return response()->json($subscriptions);
}


}
