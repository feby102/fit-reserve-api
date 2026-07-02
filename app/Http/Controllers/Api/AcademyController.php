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
    $academies = Academy::withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->latest()
        ->get();

    $academies->transform(function ($academy) {
        $academy->reviews_avg_rating = round($academy->reviews_avg_rating ?? 0, 1);
        return $academy;
    });

    return response()->json($academies);
}

public function index()
{
    $vendor = auth()->user();

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $academies = Academy::withAvg('reviews', 'rating')
        ->where('vendor_id', $vendor->id)
        ->latest()
        ->get();

    $academies->transform(function ($academy) {
        $academy->reviews_avg_rating = round($academy->reviews_avg_rating ?? 0, 1);
        return $academy;
    });

    return response()->json($academies);
}

 public function publicShow(int $id)
{
    $academy = Academy::with([
        'plans',
        'services',
        'reviews' => function ($q) {
            $q->where('is_hidden', false)->with('user:id,name');
        },
        'videos'
    ])->findOrFail($id);

    $average = $academy->reviews->avg('rating');

    return response()->json([
        'academy' => $academy,
        'average_rating' => round($average ?? 0, 1),
    ]);
} 

 public function show($id)
{
    $vendor = auth()->user();

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $academy = Academy::with([
        'plans',
        'services',
        'reviews.user'
    ])
    ->where('vendor_id', $vendor->id)
    ->findOrFail($id);

    $studentsCount = DB::table('academy_subscriptions')
        ->join('academy_plans', 'academy_plans.id', '=', 'academy_subscriptions.academy_plan_id')
        ->where('academy_plans.academy_id', $id)
        ->count();

    $revenue = DB::table('academy_subscriptions')
        ->join('academy_plans', 'academy_plans.id', '=', 'academy_subscriptions.academy_plan_id')
        ->where('academy_plans.academy_id', $id)
        ->sum('academy_plans.price');

    return response()->json([
        'academy' => $academy,
        'students_count' => $studentsCount,
        'revenue' => $revenue
    ]);
}


//create new academy
public function store(Request $request)
    {

        $request->validate([
                'type' => 'required|string',
            'name'=>'required',
            'location'=>'required',
            'price_per_hour'=>'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   

            

]);
$vendor = auth()->user();
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}

  if ($request->hasFile('image')) {
            $path = $request->file('image')->store('academies', 'public');
            $data['image'] = $path;   
        }
$academy = Academy::create([
    'vendor_id' => $vendor->id,
'type' => $request->type,
    'name'=>$request->name,
    'location'=>$request->location,
    'price_per_hour'=>$request->price_per_hour,
'image' => $data['image'] ?? null,]);
  return response()->json([
            'message'=>'Academy created',
            'academy'=>$academy
        ]);

    }




    public function update(Request $request,$id){

$vendor = auth()->user();
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$academy = Academy::where('vendor_id', $vendor->id)->findOrFail($id);
$academy->update([

            'type'=>$request->type??$academy->type ,
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



$vendor = auth()->user();
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


    public function topAcademies()
{
    $top = Academy::withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->withCount('subscriptions')   
        ->orderByDesc('reviews_avg_rating')
        ->take(5)
        ->get();

    return response()->json($top);
}

}