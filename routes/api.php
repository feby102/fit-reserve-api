<?php

use App\Http\Controllers\APi\AcademyController;
use App\Http\Controllers\APi\AcademyPlanController;
use App\Http\Controllers\APi\AcademyServiceController;
use App\Http\Controllers\APi\AcademySubscriptionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\APi\CoachBookingController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\GymController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PlayerRatingController;
use App\Http\Controllers\APi\PrivateCoachController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\APi\ReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StadiumController;
use App\Http\Controllers\Api\StadiumScheduleController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoreController as ApiStoreController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\WalletController;
 use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 
  


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgot']);
Route::post('/reset-password', [AuthController::class, 'reset']);


  Route::middleware('auth:sanctum')->group(function(){

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/wallet',[WalletController::class, 'show']);
Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);


Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/my-bookings', [BookingController::class, 'myBookings']);
Route::get('/bookings/available-slots', [BookingController::class, 'getAvailableSlots']);
Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus']);


  
Route::get('/stadiums',[StadiumController::class,'publicIndex']);
Route::get('/stadiums/{id}',[StadiumController::class,'publicShow']);

  
    
Route::post('/stadium-schedules/{stadium_id}', [StadiumScheduleController::class, 'store']);
Route::get('/stadium-schedules/{stadium_id}',[StadiumScheduleController::class, 'publicIndex']);


Route::get('/stores',[StoreController::class,'publicIndex']);
Route::get('/stores/{id}',[StoreController::class,'publicShow']);



Route::get('/academies',[AcademyController::class,'publicIndex']);
Route::get('/academies/{id}',[AcademyController::class,'publicShow']);



Route::get('/academies/{academy}/plans',[AcademyPlanController::class,'index']);

Route::get('/my-academy-subscriptions',[AcademySubscriptionController::class, 'mySubscriptions']);
Route::post('/academy-subscriptions',[AcademySubscriptionController::class, 'store']);


Route::get('/academies/{academy}/services',[AcademyServiceController::class,'index']);


 Route::get('/coaches', [PrivateCoachController::class, 'publicIndex']);
 Route::get('/coaches/{id}', [PrivateCoachController::class, 'publicShow']);
Route::get('coaches/top', [PrivateCoachController::class, 'topCoaches']);


Route::post('/coach-book',[CoachBookingController::class,'store']);


Route::post('/reviews',[ReviewController::class,'store']);
Route::post('/reviews/{id}/hide',[ReviewController::class,'hide']);
Route::get('/reviews/{type}/{id}/average',[ReviewController::class,'average']);



 Route::get('/PublicIndex',[GymController::class,'PublicIndex']);
Route::get('/gym-plans',[GymController::class,'showplans']);
Route::post('/gym-subscribe',[GymController::class,'subscribe']) ;


Route::get('/challenges', [ChallengeController::class, 'publicIndex']);
Route::get('/challenges/{id}', [ChallengeController::class, 'publicShow']);
Route::post('/challenges/{id}/join', [ChallengeController::class, 'join']);
Route::post('/challenges/{id}/pay', [ChallengeController::class, 'payForChallenge']);


Route::post('/challenges/{challenge_id}/rate-player', [PlayerRatingController::class, 'ratePlayer']);
Route::get('/player/{user_id}/ratings', [PlayerRatingController::class, 'getPlayerRatings']);


Route::get('/categories', [CategoryController::class, 'publicIndex']);
Route::get('/categories/{id}', [CategoryController::class, 'publicShow']);


Route::get('/products',[ProductController::class,'index']);

    Route::post('/orders', [OrderController::class, 'store']);
 

 Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);

    Route::post('/checkout', [CartController::class, 'checkout']);



 Route::post('/orders/{id}/pay', [PaymentController::class, 'pay']);


Route::get('/coupons',[CouponController::class,'index']);


Route::get('videos',[VideoController::class,'index']);
Route::delete('videos/{id}',[VideoController::class,'destroy']);
Route::get('videos/{id}/stats',[VideoController::class,'stats']);
Route::get('/latestVideos', [VideoController::class, 'latestVideos']);           
Route::post('/videos', [VideoController::class, 'store']);
Route::post('/videos/{id}/report', [VideoController::class, 'report']);



  Route::get('chats',[ChatController::class,'index']); 
  Route::post('chats/{id}/close',[ChatController::class,'closeConversation']);
  Route::post('users/{id}/block',[ChatController::class,'blockUser']); 
  Route::get('chats/reports',[ChatController::class,'reports']);  
  Route::get('chats/flagged',[ChatController::class,'flaggedMessages']); 
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    

    Route::get('/conversations', [ConversationController::class, 'index']);           
    Route::post('/conversations', [ConversationController::class, 'store']);          
    Route::post('/conversations/{conversation}/assign', [ConversationController::class, 'assignuser']);  

  


Route::get('/my-notifications', [NotificationController::class, 'myNotifications']);


 
Route::get('/pages', [PageController::class, 'index']);
Route::get('/pages/{id}', [PageController::class, 'show']);

 


// transfer
Route::post('wallet/transfer',[UserController::class,'transfer']);



});


 
 




Route::middleware('auth:sanctum')->prefix('/vendor')->group(function(){

Route::apiResource('/stadiums',StadiumController::class);
    Route::get('/stadiums/{id}/stats',[StadiumController::class, 'stats']);
 Route::get('/stadium/heatmap',[StadiumController::class, 'heatmap']);
Route::post('/stadium-schedules/{stadium_id}',[StadiumScheduleController::class, 'store']);
Route::get('/stadium-schedules/{stadium_id}',[StadiumScheduleController::class, 'index']);


 Route::get('/academy-plans',[AcademyPlanController::class, 'vendorPlans']);
Route::post('/academy-plans',[AcademyPlanController::class,'store']);
Route::put('/academy-plans/{id}',[AcademyPlanController::class,'update']);
Route::delete('/academy-plans/{id}',[AcademyPlanController::class,'destroy']);


Route::apiResource('/academies',AcademyController::class);

Route::post('/academy-services',[AcademyServiceController::class,'store']);
Route::put('/academy-services/{id}',[AcademyServiceController::class,'update']);
Route::delete('/academy-services/{id}',[AcademyServiceController::class,'destroy']);
Route::post('/academy-services/{id}/toggle',[AcademyServiceController::class,'toggle']);
Route::get('/academy-services/{id}/stats', [AcademyServiceController::class, 'statistics']);
Route::get('/academy-services',[AcademyServiceController::class,'vendorservice']);
Route::get('/mostRequestedServices',[AcademyServiceController::class, 'mostRequestedServices']);


Route::get('academy-subscriptions',[AcademySubscriptionController::class, 'vendorIndex']);
Route::post('/academy-subscribe',[AcademySubscriptionController::class, 'store']);


Route::apiResource('/stores',StoreController::class);


Route::get('/vendorProducts',[ProductController::class,'vendorProducts']);
Route::put('/products/{product}',[ProductController::class,'update']);
Route::post('/products',[ProductController::class,'store']);
Route::delete('/products/{id}',[ProductController::class,'destroy']);



    Route::get('/categories', [CategoryController::class, 'vendorIndex']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


    Route::get('/orders', [OrderController::class, 'vendorOrders']);
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/statistics', [OrderController::class, 'statistics']);



Route::apiResource('gyms', GymController::class);
Route::post('/gyms/{id}/plans',[GymController::class,'storePlans']);
Route::put('/gym-plans/{id}',[GymController::class,'updateplan']);
Route::delete('/gym-plans/{id}',[GymController::class,'deleteplan']);
Route::get('/gym-subscriptions',[GymController::class,'showsubscribes']);
Route::delete('/gym-subscriptions/{id}',[GymController::class,'deletesubscription']);
Route::get('/gyms/{id}/subscribers',[GymController::class,'subscribers']);
Route::post('/gym-auto-renew',[GymController::class,'handleAutoRenewal']);
Route::get('/gym-schedule/{id}',[GymController::class,'setSchedule']);


Route::apiResource('private-coaches',PrivateCoachController::class);
Route::post('/coaches/location', [PrivateCoachController::class, 'addLocation']);
Route::post('/coaches/service', [PrivateCoachController::class, 'addService']);


Route::get('/my-coach-bookings',[CoachBookingController::class,'myBookings']);
Route::get('/coach/{id}/stats',[CoachBookingController::class,'coachStats']);



Route::get('/vendorBookings',[ BookingController::class,'vendorBookings']);
 

Route::post('payments/{id}/status',[PaymentController::class,'updateStatus']);
Route::get('payments/stats',[PaymentController::class,'stats']);
Route::get('payments/export/excel',[PaymentController::class,'exportExcel']);
Route::get('payments/export/pdf',[PaymentController::class,'exportPDF']);




Route::post('coupons',[CouponController::class,'store']);
Route::put('coupons/{id}',[CouponController::class,'update']);
Route::delete('coupons/{id}',[CouponController::class,'destroy']);
Route::get('coupons',[CouponController::class,'vendorcoupon']);




Route::post('notifications/user',[NotificationController::class,'sendToUser']);
Route::post('notifications/all',[NotificationController::class,'sendToAll']);
Route::post('notifications/role',[NotificationController::class,'sendToRole']);
 



Route::get('/ongoing',[ChallengeController::class, 'ongoingCallenge']);
 Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::get('/challenges/{id}', [ChallengeController::class, 'vendorShow']);
    Route::post('/challenges', [ChallengeController::class, 'store']);
    Route::put('/challenges/{id}', [ChallengeController::class, 'update']);
    Route::post('/challenges/{id}/status', [ChallengeController::class, 'status']);
    Route::post('/participants/{id}', [ChallengeController::class, 'updateParticipant']);
    Route::post('/participants/{id}/ban', [ChallengeController::class, 'banParticipant']);
    Route::get('/challenges/{id}/stats', [ChallengeController::class, 'stats']);
  Route::patch('/challenges/{id}/status', [ChallengeController::class, 'status']);


Route::get('/vendor/report', [ReportController::class, 'myReport']);
Route::get('/reports/daily',[ReportController::class,'dailyReport']);
Route::get('/reports/weekly',[ReportController::class,'weekly']);
Route::get('/reports/monthly',[ReportController::class,'monthly']);
Route::get('/reports/yearly',[ReportController::class,'yearly']);
Route::get('/reports/vendorCoachReviews',[ReportController::class,'vendorCoachReviews']);
Route::get('/reports/best-stadiums',[ReportController::class,'bestStadiums']);
Route::get('/reports/best-services',[ReportController::class,'bestServices']);




 Route::get('/usersGrowth',[UserController::class, 'usersGrowth']);
Route::get('/latestVideos',[VideoController::class,'latestVideos']);


  
});










Route::middleware(['auth:sanctum','admin'])->group(function () {

      Route::post('/stadiums/{id}/approve',[StadiumController::class, 'approve']);
    Route::post('/stadiums/{id}/reject', [StadiumController::class, 'reject']);


Route::post('/academy-type',[AcademyController::class,'storeAcademyType']);


    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);

   Route::apiResource('banners',BannerController::class);
 
 
   Route::post('/stores/{id}/toggle', [StoreController::class, 'toggleStatus']);



Route::post('videos/{id}/approve',[VideoController::class,'approve']);
Route::post('videos/{id}/reject',[VideoController::class,'reject']);
Route::get('videos/{id}/reports',[VideoController::class,'reports']);



    Route::get('/', [UserController::class, 'index']);  
    Route::patch('{id}/toggle-active', [UserController::class, 'toggleActive']);  
    Route::patch('{id}/verify', [UserController::class, 'verifyAccount']);  
    Route::get('{id}/wallet', [UserController::class, 'wallet']);  
    Route::get('users/{id}',[UserController::class,'show']);
    Route::post('users/{id}',[UserController::class,'update']);
    Route::delete('users/{id}',[UserController::class,'destroy']);
    Route::get('ranking',[UserController::class,'ranking']);
    Route::get('/totaluser',[UserController::class, 'totaluser']);




    Route::post('/pages', [PageController::class, 'store']);
    Route::put('/pages/{id}', [PageController::class, 'update']);
    Route::delete('/pages/{id}', [PageController::class, 'destroy']);
});

