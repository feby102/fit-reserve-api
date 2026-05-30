<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use Storage;

class StadiumController extends Controller
{   use AuthorizesRequests;


public function publicIndex()
{
    $stadiums = Stadium::withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->latest()
        ->get()
        ->makeHidden(['vendor_id', 'status', 'created_at', 'updated_at']);

     $stadiums->transform(function ($item) {
        $item->reviews_avg_rating = round($item->reviews_avg_rating ?? 0, 1);
        return $item;
    });

    return response()->json($stadiums);
}

public function index()
{
    
$vendor = auth()->user();

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $stadiums = Stadium::where('vendor_id', $vendor->id)
        ->where('status', 'approved')
        ->withAvg('reviews', 'rating')   
        ->latest()
        ->get()
        ->makeHidden(['vendor_id', 'status', 'created_at', 'updated_at']);

    $stadiums->transform(function ($item) {
        $item->reviews_avg_rating = round($item->reviews_avg_rating ?? 0, 1);
        return $item;
    });

    return response()->json($stadiums);
}


public function publicShow(int $id)
{
    $stadium = Stadium::with([
    'reviews' => function ($q) {
        $q->where('is_hidden', false)
          ->with('user:id,name');
    },
    'videos','schedules'
])->findOrFail($id);

    $stadium->makeHidden([
        'id','vendor_id','status','created_at','updated_at'
    ]);

    $average = $stadium->reviews->avg('rating');

    return response()->json([
        'stadium' => $stadium,
        'average_rating' => round($average ?? 0, 1),
    ]);
}


    



    public function show($id)
    {
$vendor = auth()->user();
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
$Stadium = Stadium::where('vendor_id', $vendor->id)->findOrFail($id)
->makeHidden(['id','vendor_id','status','created_at','updated_at']);

   
 return response()->json([

            'Stadium'=>$Stadium,

             ]);
}




//create 

    public function store(Request $request)
    {
$vendor = auth()->user();
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

         $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'price_per_hour' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   
        ]);

         if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stadiums', 'public');
            $data['image'] = $path;   
        }

         $stadium = Stadium::create(array_merge($data, [
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]));

        return response()->json([
            'message' => 'Stadium created successfully',
            'stadium' => $stadium
        ], 201);
    }





//update

public function update(Request $request, $id)
    {
$vendor = auth()->user();

if (!$vendor) return response()->json(['message' => 'Unauthorized'], 403);

        $stadium = Stadium::where('vendor_id', $vendor->id)->findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'price_per_hour' => 'sometimes|numeric',
            'city' => 'sometimes|string',
            'address' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         if ($request->hasFile('image')) {
             if ($stadium->image) {
                Storage::disk('public')->delete($stadium->image);
            }
            $data['image'] = $request->file('image')->store('stadiums', 'public');
        }

        $stadium->update($data);

        return response()->json(['message' => 'Updated successfully']);
    }




   //delete



  public function destroy(Request $request, $id)
{

$vendor = auth()->user();
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
     $stadium = Stadium::where('vendor_id', $vendor->id)->findOrFail($id);
 
    $stadium->delete();

    return response()->json([

        'message' => 'Deleted'

    ]);
}




//approve
public function approve($id)
{
 
    $stadium = Stadium::findOrFail($id);
$stadium->update(['status'=>'approved']);

return response()->json([

        'message' => 'Approved'

    ]);
}





//reject
public function reject($id)
{ 

    $stadium = Stadium::findOrFail($id);
$stadium->update(['status' => 'rejected']);

return response()->json([

        'message' => 'Rejected'

    ]);
}



    public function stats($stadium_id)

{        $vendor = auth()->user();


    $stadium = Stadium::where('vendor_id', $vendor->id)->findOrFail($stadium_id);
    $total_bookings = $stadium->bookings()->count();
    $total_revenue = $stadium->bookings()->sum('total_price');
    $today_revenue = $stadium->bookings()->whereDate('created_at',Carbon::today())->sum('total_price');
    $month_revenue = $stadium->bookings()->whereMonth('created_at',Carbon::now()->month )->sum('total_price');
    $total_hours = $stadium->bookings()->sum('hours');
    return response()->json([
        'stadium' => $stadium->name,
        'total_bookings' => $total_bookings,
        'total_revenue' => $total_revenue,
        'today_revenue' => $today_revenue,
        'month_revenue' => $month_revenue,
        'total_hours' => $total_hours

    ]);

}
public function heatmap()
{
    $stadiums = Booking::where('bookable_type', Stadium::class)
    ->select('bookable_id', DB::raw('count(*) as total'))
    ->groupBy('bookable_id')
    ->orderByDesc('total')
    ->get();

    return response()->json($stadiums);
}



public function topStadiums()
{
    $top = Stadium::withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->withCount('bookings')
        ->orderByDesc('reviews_avg_rating') 
        ->orderByDesc('bookings_count')  
                ->take(5)
        ->get()
        ->makeHidden(['vendor_id', 'status']);

    return response()->json($top);
}

}