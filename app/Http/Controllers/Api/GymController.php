<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymplansResource;
use App\Http\Resources\GymScheduleResource;
use App\Http\Resources\SubscriptionsResource;
use App\Models\Gym;
use App\Models\GymPlan;
use App\Models\GymSchedule;
use App\Models\GymSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GymController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function PublicIndex()
    {
        return Gym::with('plans','schedules','services')->get()->makeHidden(['vendor_id','id']);;

    }



public function index()
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $gyms = Gym::with('plans','schedules','services')
        ->where('vendor_id', $vendor->id)
        ->latest()
        ->get();

    return response()->json($gyms);
}




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
'name'=>'required',
'type'=>'required',
'location'=>'required',
'description'=>'required']  );

$vendor = auth()->user()->vendor;
     if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }


$gym=Gym::create([
    'name'=>$data['name'],
'type'=>$data['type'],
'location'=>$data['location'],
'description'=>$data['description'],
'vendor_id' => $vendor->id]);

return \response()->json([ 'message'=>'Gym created',
            'Gym'=>$gym]);
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {    $vendor = auth()->user()->vendor;

    $gym = Gym::where('vendor_id', $vendor->id)->findOrFail($id);

$gym->update($request->all());

return $gym;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
 $vendor = auth()->user()->vendor;

    $gym = Gym::where('vendor_id', $vendor->id)->findOrFail($id);

return response()->json([

'message'=>'deleted'

]);
    }

public function subscribe(Request $request)
{
    $user = auth()->user();

    $data = $request->validate([
        'plan_id' => 'required|exists:gym_plans,id'
    ]);

    $plan = GymPlan::findOrFail($data['plan_id']);
    $gym = $plan->gym;

    if (!$gym) {
        return response()->json(['message' => 'Gym not found'], 404);
    }

    $start = now();
    $end = now()->addDays($plan->duration);

    $subscription = GymSubscription::create([
        'user_id' => $user->id,
        'gym_plan_id' => $plan->id,
        'start_date' => $start,
        'end_date' => $end,
        'status' => 'active',
        'auto_renew' => $request->input('auto_renew', false)
    ]);

    return response()->json([
        'message' => 'Subscribed successfully',
        'subscription' => $subscription
    ]);
}



public function deletesubscription(Request $request, $id) {
        $plan = GymPlan::findOrFail($id);
         $plan->delete();
        return response()->json(['message' => 'done']);
    }

    public function showsubscribes()
{
    $scribes=GymSubscription::all();
return SubscriptionsResource::collection($scribes);

}


 


public function storePlans(Request $request, $gym_id)
{
    $vendor = auth()->user()->vendor;

    $gym = Gym::where('vendor_id', $vendor->id)->findOrFail($gym_id);

    $data = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'type' => 'required|in:weekly,monthly,3Month,6month,1year',
        'hours_per_day' => 'required|integer'
    ]);

    $plan = $gym->plans()->create($data);

    return response()->json([
        'message' => 'Plan created',
        'plan' => $plan
    ]);
}

 
 public function updatePlan(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

    $plan = GymPlan::findOrFail($id);

    if ($plan->gym->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'name' => 'sometimes|string',
        'price' => 'sometimes|numeric',
        'type' => 'sometimes|in:weekly,monthly,3Month,6month,1year',
        'hours' => 'sometimes|integer'
    ]);

    $plan->update($data);

    return response()->json([
        'message' => 'Plan updated',
        'plan' => $plan
    ]);
}
 public function deletePlan($id)
{
    $vendor = auth()->user()->vendor;

    $plan = GymPlan::findOrFail($id);

    if ($plan->gym->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $plan->delete();

    return response()->json([
        'message' => 'Plan deleted'
    ]);
}

public function showplans()
{

$plans = GymPlan::all();

return GymplansResource::collection($plans);

}



//تحديد الجداول الأسبوعية


public function setSchedule(Request $request,$id){
$gym=GymSchedule::findOrFail($id);
return  GymScheduleResource::collection($gym);
}




//متابعة عدد المشتركين
public function subscribers($gym_id)
{

$gym = Gym::with('subscriptions')->findOrFail($gym_id);

return response()->json([

'gym' => $gym->name,

'subscribers' => $gym->subscriptions->count()

]);

}


// التجديد التلقائي  
     public function handleAutoRenewal() {
$expired =GymSubscription::where('end_date','<',now())->where('auto_renew', true)->get(); 

foreach($expired as $sub){
$sub->update([
                'start_date' => now(),
                'end_date' => Carbon::parse($sub->end_date)->addMonth(),
                'status' => 'active'
            ]);

}
return  $expired->count() . "AutoRenewal";
     
}



//review
public function review($gym_id){

$gym=Gym::with('reviews')->findOrFail($gym_id);
return \response()->json($gym->reviews);
}


public function topGyms()
{
    $top = Gym::withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->withCount('subscriptions')
        ->orderByDesc('reviews_avg_rating')
        ->take(5)
        ->get();

    return response()->json($top);
}
    }
