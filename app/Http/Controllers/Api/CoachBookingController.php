<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachBooking;
use App\Models\CoachSchedule;
use App\Models\PrivateCoach;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
  
$schedule=CoachSchedule::where('id',$data['schedule_id'])->where('private_coach_id',$data['coach_id'])
                        ->where('is_booked', false)->first();
 

 if (!$schedule) {

    return response()->json([
        'message' => 'This slot is not available.'
    ],422);

}

    $wallet=$request->user()->wallet;

$coach = PrivateCoach::findOrFail($data['coach_id']);
$total_price = $coach->price_per_hour;



$settings = \App\Models\Setting::first();
$commissionRate = $settings ? $settings->commission_rate : 0;  


DB::transaction(function () use($commissionRate,$coach,$schedule,$total_price,$user,$data,$request,$wallet){

if($data['payment_method']=="visa"){
$paymentController=new PaymentController;
return $paymentController->payWithvisa($request,$total_price);
}



$booking = CoachBooking::create([
    'user_id' => $user->id,
    'private_coach_id' => $coach->id,
    'schedule_id' => $schedule->id,
    'start_time' => Carbon::parse($schedule->date.' '.$schedule->start_time),
    'end_time' => Carbon::parse($schedule->date.' '.$schedule->end_time),
    'payment_method' => $data['payment_method'],
    'total_price' => $total_price,
   'status' => 'pending'
]);

$schedule->update([
    'is_booked' => true,
]);

$settings = \App\Models\Setting::first();
    $commissionRate = $settings ? $settings->commission_rate : 0;
$commissionAmount = ($total_price * $commissionRate) / 100; 


    $vendorNetProfit = $total_price - $commissionAmount;    
    app(WalletService::class)->credit(
    $coach->vendor->wallet,
    $vendorNetProfit,
    'coach_booking',
    'Coach Booking #' . $booking->id
);
    
app(WalletService::class)->debit(
    $user,
    $total_price,
    'booking_payment',
    'Booking #' . $booking->id
);


if ($settings) {
        $settings->increment('total_admin_commissions', $commissionAmount);
    }

$wallet->transactions()->create([
    'type' => 'debit',
    'amount' => $total_price,
    'description' => 'Coach booking #' . $booking->id,
    'status' => 'confirmed'
]);
 
    



});

return response()->json(['message'=>'Coach booking successful']);


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
