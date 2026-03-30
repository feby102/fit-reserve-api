<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Booking;
use App\Models\AcademyService;
use App\Models\CoachService;
use App\Models\Facility;
use App\Models\Gym;
use App\Models\PrivateCoach;
use App\Models\Report;
use App\Models\Review;
use App\Models\Stadium;
use App\Models\Studio;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;
 use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class ReportController extends Controller
{

// public function dailyReport()
// {
//     $vendor = auth()->user()->vendor;
//     $date = Carbon::today();

//     // جلب كل البوكينجز لليوم الحالي المتعلقة بالـ vendor
//     $bookings = Booking::whereDate('created_at', $date)
//         ->where(function ($query) use ($vendor) {
//             $query->whereHasMorph('bookable', [
//                 Stadium::class,
//                 Gym::class,
                
//             ], fn($q) => $q->where('vendor_id', $vendor->id))
//             ->orWhereHasMorph('bookable', [AcademyService::class], fn($q) =>
//                 $q->whereHas('academy', fn($q2) => $q2->where('vendor_id', $vendor->id))
//             )
//             ->orWhereHasMorph('bookable', [CoachService::class], fn($q) =>
//                 $q->whereHas('privateCoach', fn($q2) => $q2->where('vendor_id', $vendor->id))
//             );
//         })
//         ->get();

//     $totalProfit = $bookings->sum('total_price');
//     $totalBookings = $bookings->count();

//     return response()->json([
//         'date' => $date->toDateString(),
//         'total_profit' => $totalProfit,
//         'total_bookings' => $totalBookings
//     ]);
// }


// public function weekly()
// {
//     $vendor = auth()->user()->vendor;
//     $start = Carbon::now()->startOfWeek();
//     $end = Carbon::now()->endOfWeek();

//     $bookings = Booking::whereBetween('created_at', [$start, $end])
//         ->whereHasMorph('bookable', [
//             Stadium::class,
//             Academy::class,
//             PrivateCoach::class,
//             Gym::class,
//             AcademyService::class,
            
//         ], function ($query) use ($vendor) {
//             $query->where('vendor_id', $vendor->id);
//         });

//     $totalProfit = $bookings->sum('total_price');
//     $totalBookings = $bookings->count();

//     $report = Report::updateOrCreate(
//         [
//             'vendor_id' => $vendor->id,
//             'type' => 'weekly',
//             'report_date' => Carbon::now()->startOfWeek()
//         ],
//         [
//             'total_profit' => $totalProfit,
//             'total_bookings' => $totalBookings
//         ]
//     );

//     return response()->json($report);
// }



// public function monthly()
// {
// $vendor=\auth()->user()->vendor;

// $bookings= Booking::whereMonth('created_at',
// Carbon::now()->month)
// ->whereHasMorph('bookable',[
//             Stadium::class,
//             Academy::class,
//             PrivateCoach::class,
//             Gym::class,
//             AcademyService::class,
            
// ],function($query) use($vendor){
//     $query->where('vendor_id',$vendor->id);
// });
//  $totalProfit = $bookings->sum('total_price');
//     $totalBookings = $bookings->count();

//     $report = Report::updateOrCreate(
//         [
//             'vendor_id' => $vendor->id,
//             'type' => 'weekly',
//             'report_date' => Carbon::now()->startOfMonth()
//         ],
//         [
//             'total_profit' => $totalProfit,
//             'total_bookings' => $totalBookings
//         ]
//     );

//     return response()->json($report);
// }



// public function yearly()
// {
// $vendor=\auth()->user()->vendor;

// $bookings = Booking::whereYear('created_at',
// Carbon::now()->year)
// ->whereHasMorph('bookable',[
//             Stadium::class,
//             Academy::class,
//             PrivateCoach::class,
//             Gym::class,
//             AcademyService::class,
            
// ],function($query) use($vendor){
//     $query->where('vendor_id',$vendor->id);
// });

//    $totalProfit = $bookings->sum('total_price');
//     $totalBookings = $bookings->count();

//     $report = Report::updateOrCreate(
//         [
//             'vendor_id' => $vendor->id,
//             'type' => 'weekly',
//             'report_date' => Carbon::now()->startOfYear()
//         ],
//         [
//             'total_profit' => $totalProfit,
//             'total_bookings' => $totalBookings
//         ]
//     );

//     return response()->json($report);
// }

  public function myReport(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:daily,weekly,monthly,yearly',
        ]);

        $type = $data['type'];
        $today = Carbon::today();

         $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found for this user'], 404);
        }

        $bookings = Booking::whereHas('bookable', function($query) use ($vendor) {
    $query->where('vendor_id', $vendor->id);
})
->when($type == 'daily', fn($q) => $q->whereDate('start_time', $today))
->when($type == 'weekly', fn($q) => $q->where('start_time', '>=', Carbon::now()->startOfWeek()))
->when($type == 'monthly', fn($q) => $q->where('start_time', '>=', Carbon::now()->startOfMonth()))
->when($type == 'yearly', fn($q) => $q->where('start_time', '>=', Carbon::now()->startOfYear()))
->get();
        $totalProfit = $bookings->sum('total_price');
        $totalBookings = $bookings->count();

         $report = Report::updateOrCreate(
            [
                'vendor_id' => $vendor->id,
                'type' => $type,
                'report_date' => $today,
            ],
            [
                'total_profit' => $totalProfit,
                'total_bookings' => $totalBookings,
            ]
        );

        return response()->json([
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name,
            'report_type' => $type,
            'report_date' => $today->toDateString(),
            'total_profit' => $totalProfit,
            'total_bookings' => $totalBookings,
        ]);
    }

 

public function bestStadiums()
{        $vendor = auth()->user()->vendor;

  $stadiums = Stadium::where('vendor_id', $vendor->id)
        ->withCount('bookings')
        ->orderByDesc('bookings_count')
        ->get();
return response()->json($stadiums);

}



public function bestServices()
{
    $vendor = auth()->user()->vendor;

    $data = Booking::where('bookable_type', AcademyService::class)
        ->whereHasMorph('bookable', [AcademyService::class], function ($q) use ($vendor) {
            $q->whereHas('academy', function ($q2) use ($vendor) {
                $q2->where('vendor_id', $vendor->id);
            });
        })
        ->select('bookable_id', DB::raw('count(*) as total'))
        ->groupBy('bookable_id')
        ->orderByDesc('total')
        ->get();

    $result = $data->map(function ($item) {
        return [
            'service_name' => $item->bookable->name ?? 'Unknown',
            'total_bookings' => $item->total
        ];
    });

    return response()->json($result);
}


public function vendorCoachReviews()
{
    $vendor = auth()->user()->vendor;

    $reviews = Review::with(['user','reviewable.academy'])
        ->where('reviewable_type',  PrivateCoach::class)
        ->whereHasMorph('reviewable', [ PrivateCoach::class], function ($query) use ($vendor) {
            $query->whereHas('academy', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });
        })
        ->get()
        ->map(function ($review) {
            return [
                'user_name' => $review->user->name,
                'coach_name' => $review->reviewable->name,
                'rating' => $review->rating,
                'comment' => $review->comment
            ];
        });

    return response()->json($reviews);
}
}