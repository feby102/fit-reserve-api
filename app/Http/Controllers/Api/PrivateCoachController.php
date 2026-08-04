<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\CoachLocation;
use App\Models\CoachService;
use App\Models\PrivateCoach;
use App\Models\User;
use App\Models\Vendor;
use Hash;
use Illuminate\Http\Request;

class PrivateCoachController extends Controller
{


 
public function publicIndex()
{
    return PrivateCoach::with([
            'academy',
            'locations',
            'services',
            'reviews' => function($q) {
                 $q->where('is_hidden', false)->latest();
            }
        ])
        ->withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->latest()
        ->get();
}



 public function  index()
    {
        $user = auth()->user();

         $academiesIds = $user->academies()->pluck('id');

         $coaches = PrivateCoach::with('academy','locations','services')
            ->whereIn('academy_id', $academiesIds)->withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->latest()->get()
             
            ;

        return response()->json($coaches);
    }

//add new PrivateCoach
 public function store(Request $request)
{
    $data = $request->validate([
        'academy_id'     => 'nullable|exists:academies,id',
        'name'           => 'required|string',
        'sport'          => 'required|string',
        'price_per_hour' => 'required|numeric',
        'bio'            => 'nullable|string',
        'image'          => 'nullable|image',
        'user_id'        => 'nullable|exists:users,id',    
    ]);

    $user = auth()->user();

     if ($user->role === 'coach' && empty($data['academy_id'])) {

         $alreadyRegistered = PrivateCoach::where('user_id', $user->id)
            ->whereNull('academy_id')
            ->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'message' => 'You are already registered as a freelance coach.'
            ], 422);
        }

         $vendor = Vendor::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'city'     => $user->city,
            'area'     => $user->area,
            'password' => $user->password,
            'balance'  => 0,
        ]);

        $data['vendor_id']  = $vendor->id;
        $data['user_id']    = $user->id;
        $data['academy_id'] = null;

    } 
     elseif (Academy::where('vendor_id', $user->id)->exists() || $user->role === 'vendor') {

        if (empty($data['academy_id'])) {
            return response()->json(['message' => 'As a vendor, you must specify an academy.'], 422);
        }

        if (empty($data['user_id'])) {
            return response()->json(['message' => 'As a vendor, you must provide a valid user_id for the coach.'], 422);
        }

         $alreadyInAcademy = PrivateCoach::where('user_id', $data['user_id'])
            ->where('academy_id', $data['academy_id'])
            ->exists();

        if ($alreadyInAcademy) {
            return response()->json([
                'message' => 'This coach is already added to this academy.'
            ], 422);
        }

         $academyExists = \App\Models\Academy::where('id', $data['academy_id'])
            ->where('vendor_id', $user->id)  
            ->exists();

        if (!$academyExists) {
            return response()->json(['message' => 'This academy does not belong to you.'], 403);
        }

        $data['vendor_id'] = $user->id;

    } 
     else {
        return response()->json([
            'message' => 'Unauthorized. Only Coaches or Vendors can perform this action.'
        ], 403);
    }

     if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('coach', 'public');
    }

     $coach = PrivateCoach::create($data);
      
    return response()->json([
        'message' => 'Coach created successfully',
        'coach'   => $coach->load('academy')
    ], 201);
}




    public function publicShow($id)
    {
        $coach = PrivateCoach::with(['academy','locations','services'
        ,'reviews'=>function($q){

           $q->where('is_hidden', false)->with('user:id,name');
        }
        
        ])->findOrFail($id);

         $average = $coach->reviews->avg('rating');
       return response()->json([
        'coach' => $coach,
        'average_rating' => round($average ?? 0, 1),
    ]);
    }


public function show($id)
{
$user = auth('user-api')->user();
$academiesIds = $user->academies()->pluck('id');

$coach = PrivateCoach::whereIn('academy_id', $academiesIds)
   ->with( 'reviews.user')->findOrFail($id);
    return response()->json($coach);

}


 

public function update(Request $request,$id)
{
$user=\auth()->user()->user;

$academy=$user->academies->pluck('id');
$coach = PrivateCoach::findOrFail($id);

$coach->update($request->all());

return response()->json($coach);

}




 

public function destroy($id)
{

  $user = auth()->user();
        $academiesIds = $user->academies()->pluck('id');
        $coach = PrivateCoach::whereIn('academy_id', $academiesIds)->findOrFail($id);

 
$coach->delete();

return response()->json([

'message'=>'deleted'

]);

}



//new location

public function addLocation(Request $request)
{

return CoachLocation::create([
'private_coach_id'=>$request->coach_id,
'location'=>$request->location

]);

}



//new service

public function addService(Request $request)
{

return CoachService::create([

'private_coach_id'=>$request->coach_id,

'name'=>$request->name,

'price'=>$request->price

]);

}


public function topCoaches()
{
         $topCoaches = PrivateCoach::withAvg(['reviews' => function ($query) {
            $query->where('is_hidden', false);
        }], 'rating')
        ->with(['academy', 'locations', 'services'])   
                ->orderByDesc('reviews_avg_rating')   
        ->take(10)   
               ->get()
        ->makeHidden(['user_id', 'status', 'created_at', 'updated_at']);

    return response()->json($topCoaches);
}






public function setSchedules(Request $request)
{

$data=$request->validate([
    'private_coach_id'=>'required|exists:private_coaches,id',
    'schedules'       => 'required|array|min:1',
    'schedules.*.day'        => 'required|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'
  , 'schedules.*.start_time' => 'required|date_format:H:i',
        'schedules.*.end_time'   => 'required|date_format:H:i|after:schedules.*.start_time',
    ]);


$user = auth()->user();
    $coach = PrivateCoach::findOrFail($request->coach_id);

if($user->role=='coach'||$coach->vendor!=){}


}







}
