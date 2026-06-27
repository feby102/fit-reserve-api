<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\CoachLocation;
use App\Models\CoachService;
use App\Models\PrivateCoach;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PrivateCoachController extends Controller
{


 
public function publicIndex()
{

return PrivateCoach::with('academy','locations','services')->get()->makeHidden(['id','user_id','status','created_at','updated_at']);

}



 public function  index()
    {
        $user = auth()->user();

         $academiesIds = $user->academies()->pluck('id');

         $coaches = PrivateCoach::with('academy','locations','services')
            ->whereIn('academy_id', $academiesIds)->get()
            ->makeHidden(['id','academy_id' ,'created_at','updated_at'])
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
    
     $vendor = \App\Models\Vendor::where('id', $user->id)->first();

    if ($vendor) {
         if (empty($data['academy_id'])) {
            return response()->json(['message' => 'As a vendor, you must specify an academy.'], 422);
        }

        // يجب تحديد الكوتش المراد إضافته من الـ request
        if (empty($data['user_id'])) {
            return response()->json(['message' => 'As a vendor, you must provide a valid user_id for the coach.'], 422);
        }

         $academyExists = \App\Models\Academy::where('id', $data['academy_id'])
                                            ->where('vendor_id', $vendor->id)  
                                            ->exists();

        if (!$academyExists) {
            return response()->json([
                'message' => 'This academy does not belong to you.'
            ], 403);
        }

         $isCoach = \App\Models\User::where('id', $data['user_id'])->where('role', 'coach')->exists();
        if (!$isCoach) {
            return response()->json(['message' => 'The provided user_id does not belong to a coach account.'], 422);
        }

    } elseif ($user->role === 'coach') {
         $data['user_id'] = $user->id;
        $data['academy_id'] = null; 
    } else {
        return response()->json([
            'message' => 'Unauthorized. Only Coaches or Vendors can perform this action.'
        ], 403);
    }    

     if ($request->hasFile('image')) {
        $path = $request->file('image')->store('coach', 'public');
        $data['image'] = $path;   
    }

 
    $coach = PrivateCoach::create($data);

    return response()->json([
        'message' => 'Coach created successfully',
        'coach'   => $coach->load('academy')
    ], 201);
}
 
    public function publicShow($id)
    {
        $coach = PrivateCoach::with('academy','locations','services')->findOrFail($id)
        ->makeHidden(['id','user_id','status','created_at','updated_at']);
        return response()->json($coach);
    }


public function show($id)
{
$user = auth('user-api')->user();
$academiesIds = $user->academies()->pluck('id');

$coach = PrivateCoach::whereIn('academy_id', $academiesIds)
    ->findOrFail($id);
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



}
