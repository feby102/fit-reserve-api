<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcademyPackage;
use App\Models\AcademyPlan;
use App\Models\AcademyService;
use App\Models\AcademySubscription;
use App\Models\Booking;
use App\Models\CoachService;
use App\Models\Coupon;
use App\Models\Facility;
use App\Models\FacilityPackage;
use App\Models\Gym;
use App\Models\GymPlan;
use App\Models\GymSubscription;
use App\Models\LoyaltyPoint;
use App\Models\Notification;
use App\Models\PrivateCoach;
use App\Models\PrivateCoachPackage;
use App\Models\Stadium;
use App\Models\StadiumPackage;
use App\Models\StadiumSchedule;
use App\Models\StadiumSubscription;
use App\Models\Studio;
use App\Models\StudioPackage;
use App\Models\Vendor;
use App\Models\Wallet;
use Carbon\Carbon;
use Carbon\Doctrine\CarbonType;
use Illuminate\Http\Request;

class BookingController extends Controller
{


public function vendorBookings()
{
    $vendor = auth()->user()->vendor;

    $bookings = Booking::whereHasMorph(
        'bookable',
        [ Stadium::class,
          Academy::class,
          PrivateCoach::class,
          Gym::class,
          Facility::class,
          Studio::class],
        function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        }
    )->latest()->get();

    return response()->json($bookings);
}



    public function store(Request $request)
    {
$data=$request->validate([

            'bookable_type' => 'required|in:stadium,academy,coach,gym,coach_service,academy_service',
            'bookable_id' => 'required|integer',
            'start_time' => 'required|date_format:H:i',
            'date'  => 'required|date',
            'hours' => 'required_if:type,daily|nullable|integer|min:1',
            'payment_method'=> 'required|in:visa,cash,wallet',
            'type' => 'required|in:daily,package',
            'package_id' => 'required_if:type,package|nullable|integer',        
            'use_points' => 'nullable|integer|min:0',
            //للاكاديميات
            'full_name'       => 'required_if:bookable_type,academy|string|max:255',
            'age'             => 'required_if:bookable_type,academy|integer',
            'parent_id_card'  => 'nullable|image|mimes:jpeg,png,jpg',  
            'personal_photo'  => 'nullable|image|mimes:jpeg,png,jpg',  
            'coupon_code'     => 'nullable|string|exists:coupons,code',  
            //للملعب
             'is_recurring'    => 'nullable|boolean', 
            'recurring_weeks' => 'required_if:is_recurring,true|integer|min:1|max:4',
]);

    $user=$request->user();
$start_datetime=Carbon::parse($data['date'].' '.$data['start_time']);

    $wallet=$request->user()->wallet;
$hours = $request->input('hours', 2);
      switch ($data['bookable_type']) {
 case 'gym':
                $bookable = Gym::findOrFail($data['bookable_id']);
                $price_per_hour = $bookable->price_per_hour;
                break;

            case 'stadium':
                $bookable = Stadium::findOrFail($data['bookable_id']);
                $price_per_hour = $bookable->price_per_hour;
                break;
            case 'academy':
                $bookable = Academy::findOrFail($data['bookable_id']);
                $price_per_hour = $bookable->price;  
                break;
            case 'coach':
    $bookable = PrivateCoach::findOrFail($data['bookable_id']);
    $price_per_hour = $bookable->price_per_hour;  
    break;

            case 'coach_service':
                    $bookable = CoachService::findOrFail($data['bookable_id']);
                    $price_per_hour = $bookable->price;  
                    break;

            case 'academy_service':
                $bookable = AcademyService::findOrFail($data['bookable_id']);
                $price_per_hour = $bookable->price_per_hour;
                break;

                 
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
$vendor = null;

 
switch ($data['bookable_type']) {
    case 'stadium':
    case 'gym':
    case 'coach': 
        $vendor = $bookable->vendor; 
        break;

    case 'academy':
        $vendor = $bookable->vendor;
        break;

    case 'academy_service':
        $vendor = $bookable->academy?->vendor;
        break;

   case 'coach_service':
     $bookable->load('privateCoach.academy.vendor', 'privateCoach.vendor');

    $coach = $bookable->privateCoach;

    if ($coach) {
        
        $vendor = $coach->vendor;

         if (!$vendor && $coach->academy) {
            $vendor = $coach->academy->vendor;
        }
    }
    
     if (!$vendor && $bookable->academy_id) {
        $vendor = $bookable->academy?->vendor;
    }
    break;
}

 if (!$vendor) {
    return response()->json([
        'message' => 'not found',
        'debug_info' => [
            'bookable_type' => $data['bookable_type'],
            'bookable_id' => $data['bookable_id']
        ]
    ], 422);
}
  if ($data['type'] == 'daily') {
        $hours = (int) $request->input('hours', 1);
        $total = $price_per_hour * $hours;
        $end_datetime = (clone $start_datetime)->addHours($hours);
    } else {
        $packageModel = 'App\\Models\\' . ucfirst($data['bookable_type']) . 
                        (in_array($data['bookable_type'], ['gym', 'academy']) ? 'Plan' : 'Package');
        $package = $packageModel::findOrFail($data['package_id']);
        $total = $package->price;

        // فحص نوع الباقة (زمنية أم ساعات)
        if (isset($package->type) && in_array($package->type, ['weekly', 'monthly', '3 Month', '6 month', '1 year'])) {
            $end_datetime = match ($package->type) {
                'weekly'  => (clone $start_datetime)->addWeek(),
                'monthly' => (clone $start_datetime)->addMonth(),
                '3 Month' => (clone $start_datetime)->addMonths(3),
                '6 month' => (clone $start_datetime)->addMonths(6),
                '1 year'  => (clone $start_datetime)->addYear(),
            };
            $hours = $package->hours_per_day ?? $package->hours ?? 2;
        } else {
            $hours = (int) ($package->hours ?? 2);
            $end_datetime = (clone $start_datetime)->addHours($hours);
        }
    } 

if($request->filled('coupon_code')){
    $coupon=Coupon::where('code',$data['coupon_code'])
    ->where('expires_at', '>', now())->first();
    if ($coupon && $coupon->isValid()) {
                $discountAmount = $coupon->calculateDiscount($total);
                $total -= $discountAmount;
            }
}

$total = max(0, $total);


//النقاط
$userAvailablePoints = $user->loyaltyPoints()->sum('points');

 if ($request->has('use_points') && $request->use_points > 0) {
    
    $pointsToUse = (int) $request->use_points;

    if ($userAvailablePoints >= $pointsToUse) {
       $pointsDiscount = $pointsToUse / 10; 
        $discountAmount += $pointsDiscount;  
        $total = max(0, $total - $pointsDiscount);
         $user->loyaltyPoints()->create([
            'points' => -$pointsToUse, 
            'type'   => 'redeem',
            'description' => 'Redeemed for booking'
        ]);
    } else {
         return response()->json(['message' =>  'Your balance is insufficient.'], 422);
    }
}


//الدفع بالفيزا 


if ($data['payment_method'] === 'visa') {

$paymentController=new \App\Http\Controllers\Api\PaymentController();
return $paymentController->payWithvisa($request,$total);

}


$settings = \App\Models\Setting::first();
$commissionRate = $settings ? $settings->commission_rate : 0;  




DB::transaction(function () use ($commissionRate,$vendor,$request, $start_datetime, $user, $wallet, $bookable, $total, $data, $hours, $end_datetime) {

$subscription = null;  

    if ($data['type'] == 'package') {
        $subscription = match ($data['bookable_type']) {
            'academy' => AcademySubscription::create([
                'user_id'         => $user->id,
                'academy_plan_id' => $data['package_id'],
                'start_date'      => $start_datetime,
                'end_date'        => $end_datetime,
            ]),
            'gym' => GymSubscription::create([
                'user_id'      => $user->id,
                'gym_plan_id'  => $data['package_id'],
                'start_date'   => $start_datetime,
                'end_date'     => $end_datetime,
                'status'       => 'active',
                'auto_renew'   => $request->auto_renew ?? false,
            ]),
            'stadium' => StadiumSubscription::create([
                'user_id'    => $user->id,
                'start_date' => $start_datetime,
                'end_date'   => $end_datetime,
            ]),
             default => null,
        };
    }



// تكرار الحجز للملعب 
$iterations = ($data['bookable_type'] == 'stadium' && $request->is_recurring) ? $request->recurring_weeks : 1;

for ($i = 0; $i < $iterations; $i++) {
     $currentStart = (clone $start_datetime)->addWeeks($i);
    
     if ($data['type'] == 'package') {
         if (isset($package->type) && in_array($package->type, ['weekly', 'monthly', '3 Month', '6 month', '1 year'])) {
            // هنا نحسب تاريخ نهاية "الباقة" نفسها لكل تكرار
            $currentEnd = match ($package->type) {
                'weekly'  => (clone $currentStart)->addWeek(),
                'monthly' => (clone $currentStart)->addMonth(),
                '3 Month' => (clone $currentStart)->addMonths(3),
                '6 month' => (clone $currentStart)->addMonths(6),
                '1 year'  => (clone $currentStart)->addYear(),
            };
        } else {
             $packageHours = (int) ($package->hours ?? 2);
            $currentEnd = (clone $currentStart)->addHours($packageHours);
        }
    } else {
         $currentEnd = (clone $currentStart)->addHours($hours);
    }

     $exists = Booking::where('bookable_type', get_class($bookable))
        ->where('bookable_id', $bookable->id)
        ->whereIn('status', ['confirmed', 'pending'])
        ->where(function ($query) use ($currentStart, $currentEnd) {
            $query->where('start_time', '<', $currentEnd)
                  ->where('end_time', '>', $currentStart);
        })->first();

    if ($exists) {
        throw new \Exception(json_encode([
            'message' => "الوقت محجوز بالفعل في الأسبوع رقم " . ($i + 1),
            'conflict' => [
                'from' => $exists->start_time,
                'to'   => $exists->end_time
            ]
        ]));
    }




    
    $idCardPath = $request->hasFile('parent_id_card') ? $request->file('parent_id_card')->store('ids') : null;
$personalPhotoPath = $request->hasFile('personal_photo') ? $request->file('personal_photo')->store('photos') : null;
$booking=Booking::create([
            'user_id'         => $user->id,
            'bookable_type'   => get_class($bookable),
            'bookable_id'     => $bookable->id,
            'package_id'      => $data['package_id'] ?? null,
           'start_time'      => $currentStart,  
        'end_time'        => $currentEnd,
            'hours'           => $hours,
            'total_price'     => ($i == 0) ? $total : 0,  
            'payment_method'  => $data['payment_method'],
            'status'          => ($data['payment_method'] == 'cash') ? 'pending' : 'confirmed',
            'coupon_code'     => $request->coupon_code,
             
            // حقول الأكاديمية
            'full_name'       => $data['full_name'] ?? null,
            'age'             => $data['age'] ?? null,
            'parent_id_card'  => $idCardPath,
             'personal_photo' => $personalPhotoPath,  
        ]);
}




if ($data['payment_method'] === 'wallet') {
      $wallet = Wallet::firstOrCreate(
    ['user_id' => $user->id],
    ['balance' => 0]
);

if ($wallet->balance < $total) {
    throw new \Exception('Insufficient wallet balance to complete the booking');
}


$settings = \App\Models\Setting::first();
    $commissionRate = $settings ? $settings->commission_rate : 0;
$commissionAmount = ($total * $commissionRate) / 100; 


    $vendorNetProfit = $total - $commissionAmount;        

$wallet->decrement('balance', $total);
$vendor->increment('balance', $vendorNetProfit);

if ($settings) {
        $settings->increment('total_admin_commissions', $commissionAmount);
    }

$wallet->transactions()->create([
    'type' => 'debit',
    'amount' => $total,
    'description' => 'booking ' . $data['bookable_type'] . ' num#' . $bookable->id,
    'status' => 'confirmed'
]);

         $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $total,
            'description' => 'booking' . $data['bookable_type'] . ' num#' . $bookable->id,
            'status' => 'confirmed'
        ]);
    }          




            // إضافة نقاط ولاء
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => 10,
                'type' => 'booking'
            ]);

            

        

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Booking Reminder',
                'message' => 'Your booking starts at ' . $data['start_time']
            ]);
        });

        return response()->json(['message' => 'Booking successful']);
    }

    // عرض الحجوزات الخاصة بالمستخدم
    public function myBookings(Request $request)
    {
        $bookings = $request->user()->bookings()->latest()->get();
        return response()->json($bookings);
    }
 


public function getAvailableSlots(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'bookable_id' => 'required|integer',
        'bookable_type' => 'required|in:stadium,academy,coach,facility,studio,gym',
    ]);


$allSlots = [
        '09:00', '10:00', '11:00', '12:00', '13:00', 
        '14:00', '15:00', '16:00', '17:00', '18:00', 
        '19:00', '20:00', '21:00', '22:00'
    ];

    $bookedSlots = Booking::where('bookable_id', $request->bookable_id)
->where('bookable_type', 'App\Models\\'.ucfirst($request->bookable_type))
        ->where('status','confirmed')->get()
        ->map(function($booking){
return Carbon::parse($booking->start_time)->format('H:i');

        })->toArray();

$availableSlots =\array_values(\array_diff($allSlots,$bookedSlots));

return response()->json([
        'date' => $request->date,
        'available_slots' => $availableSlots
    ]);

        }



        //قبول او رفض


public function updateStatus(Request $request, $id)
{
$data=$request->validate([
    'status'=>'required|in:confirmed,rejected',
    'rejection_reason' => 'required_if:status,rejected|string|max:255'
]);
$booking=Booking::findOrFail($id);

if($request->status=='confirmed'){
    $booking->update(['status' => 'confirmed']);
Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Your booking has been accepted!',
            'message' => 'The admin has approved your booking for ' . $booking->bookable->name,
        ]);
    } 
   
    else {
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason  
        ]);

if($booking->payment_method == 'wallet'){

$userWallet=$booking->user->wallet;
$userWallet->increment('balance',$booking->total_price);
$userWallet->transactions()->create([
                'type' => 'credit',
                'amount' => $booking->total_price,
                'description' => 'Refund of rejected booking amount No.' . $booking->id,
            ]);
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Unfortunately, the reservation was declined',
            'message' => 'Reason for rejection: ' . $request->rejection_reason,
        ]);
    }

    return response()->json(['message' => 'The booking status has been successfully updated.']);
}

}

 