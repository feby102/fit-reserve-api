<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;  

use App\Models\Academy;
use App\Models\AcademyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AcademyController extends Controller
{
public function publicIndex()
{
    $academies = Academy::with('type')->latest()->get();

    return response()->json($academies);
}


  public function index()
{


    $vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
    $academies = Academy::with('type')
        ->where('vendor_id', $vendor->id)
        ->latest()
        ->get();

    return response()->json($academies);
 
    }


public function publicShow($id)
{
    $academy = Academy::with([
        'type',
        'plans',
        'services',
        'reviews.user'
    ])->findOrFail($id);

    return response()->json($academy);
}




    public function show($id)
    {
$vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$academy = Academy::with([
    'type',
    'plans',
    'services',
    'reviews.user'
])
->where('vendor_id', $vendor->id)
->findOrFail($id);

   
  $studentsCount = DB::table('academy_subscriptions')
->join('academy_plans','academy_plans.id','=','academy_subscriptions.academy_plan_id')
->where('academy_plans.academy_id',$id)->count();


$revenue=DB::table('academy_subscriptions')
->join('academy_plans','academy_plans.id','=','academy_subscriptions.academy_plan_id')
 ->where('academy_plans.academy_id',$id) 
 ->sum('academy_plans.price');


 return response()->json([

            'academy'=>$academy,

            'students_count'=>$studentsCount,

            'revenue'=>$revenue

        ]);
}


//create new academy
public function store(Request $request)
    {

        $request->validate([
            'academy_type_id'=>'required|exists:academy_types,id',
            'name'=>'required',
            'location'=>'required',
            'price_per_hour'=>'required'
            

]);
$vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$academy = Academy::create([
    'vendor_id' => $vendor->id,
    'academy_type_id'=>$request->academy_type_id,
    'name'=>$request->name,
    'location'=>$request->location,
    'price_per_hour'=>$request->price_per_hour
]);
  return response()->json([
            'message'=>'Academy created',
            'academy'=>$academy
        ]);

    }




    public function update(Request $request,$id){

$vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$academy = Academy::where('vendor_id', $vendor->id)->findOrFail($id);
$academy->update([

            'academy_type_id'=>$request->academy_type_id ??$academy->academy_type_id,
            'name'=>$request->name ??$academy->name ,
            'location'=>$request->location??$academy->location
            ]);


return response()->json([

        'message'=>'Academy updated',

        'academy'=>$academy

    ]);
}

  public function destroy($id)
    {



$vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$academy = Academy::where('vendor_id', $vendor->id)->findOrFail($id);
$academy->delete();


        return response()->json([

            'message'=>'Academy deleted'

        ]);

    }



public function storeAcademyType(Request $request)
    {

        $request->validate([
             'name'=>'required',
             

]);
 $academy_type = AcademyType::create([
     'name'=>$request->name,
 ]);
  return response()->json([
            'message'=>'Academy type created',
            'academy'=>$academy_type
        ]);

    }

}