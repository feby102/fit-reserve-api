<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\CoachLocation;
use App\Models\CoachService;
use App\Models\PrivateCoach;
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
    ]);

    $user = auth()->user();

     if ($user->role !== 'Coach') {
        return response()->json([
            'message' => 'Unauthorized or you are not logged in as a coach.'
        ], 403);      }
     if (!empty($data['academy_id'])) {
         $academyExists = \App\Models\Academy::where('id', $data['academy_id'])
                                            ->where('user_id', $user->id)
                                            ->exists();

        if (!$academyExists) {
            return response()->json([
                'message' => 'This academy does not belong to you.'
            ], 403);
        }
    }

     if ($request->hasFile('image')) {
        $path = $request->file('image')->store('gyms', 'public');
        $data['image'] = $path;   
    }
 
     $data['user_id'] = $user->id;

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
