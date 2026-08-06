<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachBooking;
use App\Models\CoachSchedule;
use App\Models\PrivateCoach;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoachBookingController extends Controller
{
    public function store(Request $request)
{
$data = $request->validate([
        'coach_id' => 'required|exists:private_coaches,id',
        'schedule_id' => 'required|exists:coach_schedules,id',
        'payment_method' => 'required|in:wallet,visa,vodafone_cash',
    ]);

$user=$request->user();
$end_time=Carbon::parse($request->start_time)->addHour($request->hours);

$coach = PrivateCoach::findOrFail($request->private_coach_id);
$total_price=$request->hours*$coach->price_per_hour;



$booking = CoachBooking::create([

'user_id'=>$user->id,

'private_coach_id'=>$coach->id,

'start_time'=>$request->start_time,

'end_time'=>$end_time,

'hours'=>$request->hours,

'total_price'=>$total_price,

'status'=>'confirmed'

]);



return response()->json(['message'=>'Coach booked','booking'=>$booking]);


}

//show user's booking

public function myBookings(Request $request)
{
return CoachBooking::with('coach')->where('user_id',$request->user()->id)->get();


}


public function coachStats($coach_id)
{
    $vendor = auth()->user();

     $coach = PrivateCoach::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->findOrFail($coach_id);

     $totalBookings = CoachBooking::where('private_coach_id', $coach->id)->count();

    $revenue = CoachBooking::where('private_coach_id', $coach->id)->sum('total_price');

    return response()->json([
        'coach_id' => $coach->id,
        'total_bookings' => $totalBookings,
        'revenue' => $revenue
    ]);
}






public function availableSlots(Request $request,$coachId)
{

$day=Carbon::parse($request->day)->format('l');
$slots = CoachSchedule::where('private_coach_id',$coachId)
        ->where('day',$day)
        ->whereNull('booking_id')
        ->orderBy('start_time')
        ->get();

    return response()->json($slots);


}







}
