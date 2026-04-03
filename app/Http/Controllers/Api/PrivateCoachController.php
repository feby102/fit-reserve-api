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

return PrivateCoach::with('academy','locations','services')->get()->makeHidden(['id','vendor_id','status','created_at','updated_at']);

}



 public function  index()
    {
        $vendor = auth()->user()->vendor;

         $academiesIds = $vendor->academies()->pluck('id');

         $coaches = PrivateCoach::with('academy','locations','services')
            ->whereIn('academy_id', $academiesIds)->get()
            ->makeHidden(['id','academy_id' ,'created_at','updated_at'])
            ;

        return response()->json($coaches);
    }

//add new PrivateCoach
 
public function store(Request $request)
{

$data=$request->validate([
'academy_id'=>'required|exists:academies,id',
'name'=>'required',
'sport'=>'required',
'price_per_hour'=>'required'

]);

 $vendor = auth()->user()->vendor;
 if(!$vendor->academies()->where('id', $data['academy_id'])->exists()){
            return response()->json(['message'=>'This academy does not belong to you'], 403);
        }

$coach = PrivateCoach::create(
[
    'name'=> $data['name'],
'sport'=> $data['sport'],
'price_per_hour'=> $data['price_per_hour'],
'academy_id'=>$data['academy_id']
]

);


        return response()->json(['message'=>'Coach created','coach'=>$coach]);

}




 
    public function publicShow($id)
    {
        $coach = PrivateCoach::with('academy','locations','services')->findOrFail($id)
        ->makeHidden(['id','vendor_id','status','created_at','updated_at']);
        return response()->json($coach);
    }


public function show($id)
{

$vendor=\auth()->user()->vendor;

$academy=$vendor->academies->pluck('id');
$coach=PrivateCoach::where('academy_id',$academy)->findOrFail($id);


    return response()->json($coach);

}


 

public function update(Request $request,$id)
{
$vendor=\auth()->user()->vendor;

$academy=$vendor->academies->pluck('id');
$coach = PrivateCoach::findOrFail($id);

$coach->update($request->all());

return response()->json($coach);

}




 

public function destroy($id)
{

  $vendor = auth()->user()->vendor;
        $academiesIds = $vendor->academies()->pluck('id');
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
        ->makeHidden(['vendor_id', 'status', 'created_at', 'updated_at']);

    return response()->json($topCoaches);
}



}
