<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;


class StadiumController extends Controller
{   use AuthorizesRequests;



public function publicIndex()
{
    $stadiums = Stadium::latest()->get()->makeHidden(['id','vendor_id','status','created_at','updated_at']);

    return response()->json($stadiums);
}
 


public function index()
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $stadiums = Stadium::where('vendor_id', $vendor->id)
        ->where('status','approved')
        ->latest()
        ->get()
        ->makeHidden(['id','vendor_id','status','created_at','updated_at']);  

    return response()->json($stadiums);
}

public function publicShow($id)
{
    $Stadium = Stadium::findOrFail($id)->makeHidden(['id','vendor_id','status','created_at','updated_at']);

    return response()->json($Stadium);
}




    public function show($id)
    {
$vendor = auth()->user()->vendor;
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

    public function store(Request $request){
$vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}

 $data=$request->validate([ 
     
        'name'=>'required',
        'description'=>'nullable',
        'city'=>'required',
        'address'=>'required',
        'price_per_hour'=>'required|numeric',
       
]);

$stadium=Stadium::create([
    'vendor_id' => $vendor->id,
     'name' => $request->name, 
      'city' => $request->city,
       'address' => $request->address,  
       'price_per_hour' => $request->price_per_hour,  
       'description' => $request->description,  
       
    'status'=>'pending'
    ,$data
]);

 return response()->json($stadium);
}





//update


   public function update(Request $request,$id){
    $vendor = auth()->user()->vendor;
if (!$vendor) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
    $stadium=Stadium::where('vendor_id', $vendor->id)->findOrFail($id);
 
   $data = $request->validate([
    'name'=>'sometimes',
    'price_per_hour'=>'sometimes|numeric'
]);

$stadium->update($data);

    return response()->json([

        'message' => 'Updated'

    ]);


   }





   //delete



  public function destroy(Request $request, $id)
{

$vendor = auth()->user()->vendor;
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

{        $vendor = auth()->user()->vendor;

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

}