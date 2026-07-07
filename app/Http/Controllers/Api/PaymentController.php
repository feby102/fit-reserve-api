<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Booking;
use App\Models\LedgerEntry;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\PendingVerification;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,refunded'
        ]);

        $transaction = WalletTransaction::findOrFail($id); 
        $transaction->status = $request->status;
        $transaction->save();

        if ($request->status == 'refunded') {
            $wallet = $transaction->wallet;
            if ($transaction->type == 'credit') {
                $wallet->decrement('balance', $transaction->amount);
            } elseif ($transaction->type == 'debit') {
                $wallet->increment('balance', $transaction->amount);
            }
        }
        return response()->json(['done']);
    }

    public function stats($academy_id) 
    {
        $academy = Academy::with(['plans'])->findOrFail($academy_id);
        $totalRevenue = $academy->plans()->sum('price');

        return response()->json([
            'total_revenue' => $totalRevenue,
        ]);
    } 
     
    public function exportExcel()
    {
        $transactions = WalletTransaction::with('wallet.user')->get();
        return Excel::download(new \App\Exports\TransactionsExport($transactions), 'transactions.xlsx');
    }

    public function exportPDF()
    {
        $transactions = WalletTransaction::with('wallet.user')->get();
        $pdf = PDF::loadView('admin.payments.pdf', compact('transactions'));
        return $pdf->download('transactions.pdf');
    }

    // 1️⃣ دفع الفيزا للأوردرات العادية (تم إصلاح الـ Crash وتغيير اسم متغير الاستجابة)
    public function payWithvisa($request, $localOrder) 
    {
        $base = env("PAYMOB_BASE_URL");
        $amount_cents = $localOrder->total_price * 100;

        if ($amount_cents < 10) {
            return response()->json(['message' => 'Minimum payment is 0.10 EGP'], 400);
        }

        /* generate token */
        $tokenResponse = Http::post($base.'/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ]);
        $token = $tokenResponse->json()['token'] ?? null;

        if (!$token) {
            return response()->json($tokenResponse->json(), 500);
        }

        /* create order */
       $orderResponse = Http::post($base.'/api/ecommerce/orders', [
    'auth_token'        => $token,
    'delivery_needed'   => false,
    'amount_cents'      => $amount_cents,
    'currency'          => "EGP",
    'merchant_order_id' => $localOrder->id,
    'items'             => []
]);
        
        $paymobOrderData = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($paymobOrderData, 500);
        }

        $paymob_order_id = $paymobOrderData['id'];

        // تحديث الموديل المحلي الأصلي بنجاح دون Crash
        $localOrder->update([
            'payment_method' => 'visa',
            'paymob_order_id' => $paymob_order_id,

                     
                
        ]);

        /* generate payment key */
        $paymentKeyResponse = Http::post($base.'/api/acceptance/payment_keys', [
            "auth_token" => $token,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $paymob_order_id,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_VISA_INTEGRATION_ID'),
            "billing_data" => [
                "first_name" => "Test", "last_name" => "User", "email" => "test@test.com",
                "phone_number" => "01000000000", "apartment" => "NA", "floor" => "NA",
                "street" => "NA", "building" => "NA", "shipping_method" => "NA",
                "postal_code" => "NA", "city" => "Cairo", "country" => "EG", "state" => "NA"
            ]
        ]);
        Log::info('Payment Key Response', $paymentKeyResponse->json());

        $payment_token = $paymentKeyResponse->json()['token'] ?? null;

        if(!$payment_token){
    return response()->json($paymentKeyResponse->json(),500);
}
        $url = "https://accept.paymob.com/api/acceptance/iframes/".env('PAYMOB_IFRAME_ID')."?payment_token=".$payment_token;

        return response()->json(['payment_url' => $url]);
    }

    // 2️⃣ دفع المحفظة للأوردرات العادية
    public function payWithWallet($request, $localOrder, $phone_number)
    {
        $base = env("PAYMOB_BASE_URL");
        $amount_cents = $localOrder->total_price * 100;

        if ($amount_cents < 10) {
            return response()->json(['message' => 'Minimum payment is 0.10 EGP'], 400);
        }

        /* generate token */
        $tokenResponse = Http::post($base.'/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ]);
        $token = $tokenResponse->json()['token'] ?? null;

        if (!$token) {
            return response()->json($tokenResponse->json(), 500);
        }

      $orderResponse = Http::post($base.'/api/ecommerce/orders', [
    'auth_token'        => $token,
    'delivery_needed'   => false,
    'amount_cents'      => $amount_cents,
    'currency'          => "EGP",
    'merchant_order_id' => $localOrder->id,
    'items'             => []
]);
        Log::info('Order Response', $orderResponse->json());
        $paymobOrderData = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($paymobOrderData, 500);
        }

        $paymob_order_id = $paymobOrderData['id'];

        $localOrder->update([
            'payment_method' => 'vodafone_cash',
            'paymob_order_id' => $paymob_order_id,
            
        ]);

        /* generate payment key */
        $paymentKeyResponse = Http::post($base.'/api/acceptance/payment_keys', [
            "auth_token" => $token,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $paymob_order_id,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_WALLET_INTEGRATION_ID'),   
            "billing_data" => [
                "first_name" => "Test", "last_name" => "User", "email" => "test@test.com",
                "phone_number" => $phone_number, "apartment" => "NA", "floor" => "NA",
                "street" => "NA", "building" => "NA", "shipping_method" => "NA",
                "postal_code" => "NA", "city" => "Cairo", "country" => "EG", "state" => "NA"
            ]
        ]);

        $payment_token = $paymentKeyResponse->json()['token'] ?? null;

        if (!$payment_token) {
            return response()->json(['message' => 'Failed to generate payment key'], 500);
        }

        $walletResponse = Http::post($base.'/api/acceptance/payments/pay', [
            "source" => [
                "identifier" => $phone_number,
                "subtype" => "WALLET"
            ],
            "payment_token" => $payment_token
        ]);

        $walletData = $walletResponse->json();
        $url = $walletData['iframe_redirection_url']
            ?? $walletData['iframe_url']
            ?? $walletData['redirection_url']
            ?? $walletData['redirect_url']
            ?? null;

        if (!$url) {
            return response()->json(['message' => 'Failed to generate wallet payment url', 'error' => $walletData], 500);
        }

        return response()->json(['payment_url' => $url]);
    }

    public function pay(Request $request, $order_id)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:app_wallet,visa,vodafone_cash',
            'phone_number' => 'required_if:payment_method,vodafone_cash|string' 
        ]);

        $user = auth()->user();
        $order = Order::where('user_id', $user->id)->findOrFail($order_id);

        if ($order->payment_status == 'paid') {
            return response()->json(['message' => 'Already paid']);
        }

        if ($data['payment_method'] == 'app_wallet') {
            $wallet = $user->wallet;

            if ($wallet->balance < $order->total_price) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }

            DB::transaction(function () use ($wallet, $order) {
                $wallet->decrement('balance', $order->total_price);
                $wallet->transactions()->create([
                    'type' => 'debit',
                    'amount' => $order->total_price,
                    'description' => 'Order payment #' . $order->id
                ]);

                $order->update([
                    'payment_method' => 'app_wallet',
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
            });

            return response()->json([
                'message' => 'Paid successfully using wallet',
                'order' => $order
            ]);
        }

        if ($data['payment_method'] == 'visa') {
            return $this->payWithvisa($request, $order);
        }

        if ($data['payment_method'] == 'vodafone_cash') {
            return $this->payWithWallet($request, $order, $data['phone_number']);
        }
    }
public function webhook(Request $request)
{ 


Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));

Log::info('Step 1');

$obj = $request->input('obj');

Log::info('Step 2');

$paymobOrderId = $obj['order']['id'] ?? null;

Log::info('Step 3');

$merchantOrderId = $obj['order']['merchant_order_id'] ?? null;

Log::info('Step 4');

$order = Order::where('id', $merchantOrderId)->first();

Log::info('Step 5');







    Log::info('Paymob Webhook Reached');
    Log::info('Webhook Payload', $request->all());

$obj = $request->input('obj');

Log::info('Obj', [$obj]);
    $obj = $request->input('obj');
    if (!$obj) {
        return response()->json(['message' => 'Invalid payload'], 400);
    }

    $secret = env('PAYMOB_HMAC_SECRET'); 

    // 1. تعريف وتأمين المتغيرات في أول الدالة لعدم حدوث Undefined variable
    $paymobOrderId = $obj['order']['id'] ?? null;

    Log::info('Merchant Order ID', [
    'merchant_order_id' => $obj['order']['merchant_order_id'] ?? null,
    'paymob_order_id' => $obj['order']['id'] ?? null,
]);


    $merchantOrderId = $obj['order']['merchant_order_id'] ?? null;
    $transactionId = $obj['id'] ?? null;
    $isSuccess = (isset($obj['success']) && ($obj['success'] === true || $obj['success'] === 'true'));
Log::info('Paymob Data', [
    'paymob_order_id' => $paymobOrderId,
    'merchant_order_id' => $merchantOrderId,
    'success' => $isSuccess,
]);
    $booking = null;
    $order = null;


    // 2. التحقق من الـ HMAC للتأمين
    $hmacData = [
        $obj['amount_cents'] ?? '', $obj['created_at'] ?? '', $obj['currency'] ?? '',
        (isset($obj['error_occured']) && ($obj['error_occured'] === true || $obj['error_occured'] === 'true')) ? 'true' : 'false',
        (isset($obj['has_parent_transaction']) && ($obj['has_parent_transaction'] === true || $obj['has_parent_transaction'] === 'true')) ? 'true' : 'false',
        $obj['id'] ?? '', $obj['integration_id'] ?? '',
        (isset($obj['is_3d_secure']) && ($obj['is_3d_secure'] === true || $obj['is_3d_secure'] === 'true')) ? 'true' : 'false',
        (isset($obj['is_auth']) && ($obj['is_auth'] === true || $obj['is_auth'] === 'true')) ? 'true' : 'false',
        (isset($obj['is_capture']) && ($obj['is_capture'] === true || $obj['is_capture'] === 'true')) ? 'true' : 'false',
        (isset($obj['is_voided']) && ($obj['is_voided'] === true || $obj['is_voided'] === 'true')) ? 'true' : 'false',
        $obj['order']['id'] ?? '', $obj['owner'] ?? '',
        (isset($obj['pending']) && ($obj['pending'] === true || $obj['pending'] === 'true')) ? 'true' : 'false',
        $obj['source_data']['pan'] ?? '', $obj['source_data']['sub_type'] ?? '', $obj['source_data']['type'] ?? '',
        (isset($obj['success']) && ($obj['success'] === true || $obj['success'] === 'true')) ? 'true' : 'false',
    ];

    $hmacString = implode('', $hmacData);
    $calculated_hmac = hash_hmac('sha512', $hmacString, $secret);
    $hmac = $request->query('hmac') ?? $request->input('hmac') ?? '';

    // if (!hash_equals($calculated_hmac, $hmac)) {
    //     Log::error('HMAC Mismatch!');
    //     return response()->json(['message' => 'Invalid HMAC'], 403);
    // }

    if (!$paymobOrderId) {
        return response()->json(['message' => 'Paymob Order ID missing'], 200);
    }



Log::info('Webhook Flags', [
    'success' => $obj['success'] ?? null,
    'pending' => $obj['pending'] ?? null,
    'is_voided' => $obj['is_voided'] ?? null,
    'is_refunded' => $obj['is_refunded'] ?? null,
    'is_capture' => $obj['is_capture'] ?? null,
]);




    if (!$isSuccess) {
        Log::info('Transaction not successful yet for order: ' . $paymobOrderId);
        return response()->json(['message' => 'Transaction not successful'], 200);
    }

    // ---------------------------------------------------------
    // البحث والمعالجة الآمن
    // ---------------------------------------------------------
    
    // أولاً: الحجوزات (الملاعب والجيم)
    $booking = \App\Models\Booking::where('id', $merchantOrderId)->first();
    
    // لو ملقيناش بالـ merchant_order_id، وفيه عمود في الجدول اسمه paymob_order_id، ندور بيه كـ حماية احتياطية
    if (!$booking && \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'paymob_order_id')) {
        $booking = \App\Models\Booking::where('paymob_order_id', $paymobOrderId)->first();
    }

    if ($booking) {
        if ($booking->payment_status == 'paid') {
            return response()->json(['message' => 'Booking already processed'], 200);
        }
try {

    DB::transaction(function () use ($order, $transactionId, $paymobOrderId) {
  $updateData = [
        'payment_status'  => 'paid',
        'status'          => 'confirmed',
    ];
    if (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'paymob_order_id')) {
        $updateData['paymob_order_id'] = $paymobOrderId;
    }
    $booking->update($updateData);

    $stadium = $booking->stadium;
    $owner = $stadium->vendor ?? $stadium->user ?? null;


Log::info('Admin',[
    'admin'=>$admin
]);

    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        Log::error('No admin user found - cannot process payout for booking ' . $booking->id);
        return;
    }

    $amount = $booking->total_price;
    $platformFee = $amount * 0.10;
    $ownerAmount = $amount - $platformFee;

    $walletService = app(\App\Services\WalletService::class);


    Log::info('Admin', [
    'id' => $admin?->id,
    'role' => $admin?->role,
]);
    // الفلوس كلها بتدخل الأدمن الأول
    $walletService->credit($admin, $amount, 'credit', "Booking #{$booking->id} - gross amount received from Paymob");

    if ($owner) {
        // بيتحول نصيب الفيندور
        $walletService->debit($admin, $ownerAmount, 'debit', "Booking #{$booking->id} - transferred to vendor #{$owner->id}");
        $walletService->credit($owner, $ownerAmount, 'credit', "Booking #{$booking->id} payout");
    } else {
        Log::warning("Stadium owner/vendor not found for booking ID: {$booking->id}");
    }
});
 
} catch (\Throwable $e) {

    Log::error($e->getMessage());
    Log::error($e->getFile());
    Log::error($e->getLine());

    throw $e;
}

return response()->json(['message' => 'Booking paid successfully'], 200);
    }

    
    // ثانياً: الأوردرات العادية للمنتجات
    $order = \App\Models\Order::where('id', $merchantOrderId)->first();

Log::info('Paymob Data', [
    'paymob_order_id' => $paymobOrderId,
    'merchant_order_id' => $merchantOrderId,
    'success' => $isSuccess,
]);



    if (!$order && \Illuminate\Support\Facades\Schema::hasColumn('orders', 'paymob_order_id')) {
        $order = \App\Models\Order::where('paymob_order_id', $paymobOrderId)->first();
    }
Log::info([
    'merchant_order_id' => $merchantOrderId,
    'paymob_order_id' => $paymobOrderId,
    'order_found' => $order ? true : false,
]);
    if ($order) {


    Log::info($order->toArray());
        if ($order->payment_status == 'paid') {
            return response()->json(['message' => 'Order already processed'], 200);
        }
\DB::transaction(function () use ($order, $transactionId, $paymobOrderId) {
    $updateData = [
        'payment_status'  => 'paid',
        'status'          => 'confirmed',
    ];
    if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'paymob_order_id')) {
        $updateData['paymob_order_id'] = $paymobOrderId;
    }
    $order->update($updateData);

    $order->load('items.product.seller');
    $walletService = app(\App\Services\WalletService::class);

    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        Log::error('No admin user found - cannot process payout for order ' . $order->id);
        return;
    }

    foreach ($order->items as $item) {


       Log::info('Seller Debug',[
        'product_id'=>$item->product_id,
        'seller_id'=>$item->product->seller_id ?? null,
        'seller'=>$item->product->seller ?? null,
    ]);

        $seller = $item->product->seller ?? null;
        if (!$seller) continue;

        $total = $item->price * $item->quantity;
        $platformFee = $total * 0.10;
        $sellerAmount = $total - $platformFee;
Log::info('Crediting admin');

Log::info('Admin', [
    'id' => $admin?->id,
    'role' => $admin?->role,
]);



        // 1) الفلوس كلها بتدخل محفظة الأدمن الأول (تمثل رصيد باي موب)
        $walletService->credit(
            $admin,
            $total,
            'credit',
            "Order #{$order->id} - gross amount received from Paymob"
        );


        Log::info('Seller credited');
Log::info('Debiting admin');

        // 2) بيتخصم نصيب الفيندور من الأدمن
try {

    $walletService->debit(
        $admin,
        $sellerAmount,
        'debit',
        "Order #{$order->id} - transferred to vendor #{$seller->id}"
    );

    $walletService->credit(
        $seller,
        $sellerAmount,
        'credit',
        "Order #{$order->id} item payout"
    );

} catch (\Throwable $e) {

    Log::error($e->getMessage());
    throw $e;
}}
});
        return response()->json(['message' => 'Order paid and confirmed successfully'], 200);
    }

    return response()->json(['message' => 'No matching records found'], 200);
}




public function payWithVisaForVerification(Request $request, $pendingVerification)
{
    $user   = auth()->user();
    $amount = $pendingVerification->price;
    $base   = env("PAYMOB_BASE_URL");
    $amount_cents = $amount * 100;

    $token = Http::post($base . '/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY')
    ])->json()['token'] ?? null;

    if (!$token) {
        return response()->json(['message' => 'Paymob auth failed'], 500);
    }

    $paymobOrder = Http::post($base . '/api/ecommerce/orders', [
        'auth_token'      => $token,
        'delivery_needed' => false,
        'amount_cents'    => $amount_cents,
        'currency'        => 'EGP',
        'items'           => []
    ])->json();

    $paymob_order_id = $paymobOrder['id'] ?? null;

    if (!$paymob_order_id) {
        return response()->json(['message' => 'Failed to create Paymob order'], 500);
    }

    // 🔥 تعديل حاسم: حفظ يدوي صريح لضمان التخزين بالداتابيز وتجنب مشاكل الـ $fillable
    $pendingVerification->paymob_order_id = $paymob_order_id;
    $pendingVerification->save();

    Log::info('Saved paymob_order_id into PendingVerification ID: ' . $pendingVerification->id . ' with Order ID: ' . $paymob_order_id);

    $response = Http::post($base . '/api/acceptance/payment_keys', [
        'auth_token'     => $token,
        'amount_cents'   => $amount_cents,
        'expiration'     => 3600,
        'order_id'       => $paymob_order_id,
        'currency'       => 'EGP',
        'integration_id' => env('PAYMOB_VISA_INTEGRATION_ID'),
        'billing_data'   => [
            'first_name'      => $user->name ?? 'User',
            'last_name'       => $user->name ?? 'Test',
            'email'           => $user->email ?? 'test@test.com',
            'phone_number'    => $user->phone ?? '01000000000',
            'apartment'       => 'NA', 'floor' => 'NA', 'street' => 'NA', 'building' => 'NA',
            'shipping_method' => 'NA', 'postal_code' => '12345', 'city' => 'Cairo', 'country' => 'EG', 'state' => 'Cairo',
        ]
    ]);

    $payment_token = $response->json()['token'] ?? null;

    if (!$payment_token) {
        return response()->json(['message' => 'Failed to generate payment key'], 500);
    }

    $url = 'https://accept.paymob.com/api/acceptance/iframes/' . env('PAYMOB_IFRAME_ID') . '?payment_token=' . $payment_token;

    return response()->json(['payment_url' => $url]);
}

// 2️⃣ دفع فودافون كاش للتوثيق - نسخة معدلة ومؤمنة
public function payWithWalletForVerification(Request $request, $pendingVerification, $phone_number)
{
    $user   = auth()->user();
    $amount = $pendingVerification->price;
    $base   = env("PAYMOB_BASE_URL");
    $amount_cents = $amount * 100;

    $token = Http::post($base . '/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY')
    ])->json()['token'] ?? null;

    if (!$token) {
        return response()->json(['message' => 'Paymob auth failed'], 500);
    }

    $paymobOrder = Http::post($base . '/api/ecommerce/orders', [
        'auth_token'      => $token,
        'delivery_needed' => false,
        'amount_cents'    => $amount_cents,
        'currency'        => 'EGP',
        'items'           => []
    ])->json();

    $paymob_order_id = $paymobOrder['id'] ?? null;

    if (!$paymob_order_id) {
        return response()->json(['message' => 'Failed to create Paymob order'], 500);
    }

    // 🔥 تعديل حاسم: حفظ يدوي صريح لضمان التخزين بالداتابيز وتجنب مشاكل الـ $fillable
    $pendingVerification->paymob_order_id = $paymob_order_id;
    $pendingVerification->save();

    Log::info('Saved paymob_order_id into PendingVerification ID: ' . $pendingVerification->id . ' with Order ID: ' . $paymob_order_id);

    $payment_token = Http::post($base . '/api/acceptance/payment_keys', [
        'auth_token'     => $token,
        'amount_cents'   => $amount_cents,
        'expiration'     => 3600,
        'order_id'       => $paymob_order_id,
        'currency'       => 'EGP',
        'integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
        'billing_data'   => [
            'first_name'      => $user->name ?? 'User',
            'last_name'       => $user->name ?? 'Test',
            'email'           => $user->email ?? 'test@test.com',
            'phone_number'    => $phone_number,
            'apartment'       => 'NA', 'floor' => 'NA', 'street' => 'NA', 'building' => 'NA',
            'shipping_method' => 'NA', 'postal_code' => 'NA', 'city' => 'Cairo', 'country' => 'EG', 'state' => 'NA',
        ]
    ])->json()['token'] ?? null;

    if (!$payment_token) {
        return response()->json(['message' => 'Failed to generate payment key'], 500);
    }

    $walletResponse = Http::post($base . '/api/acceptance/payments/pay', [
        'source' => [
            'identifier' => $phone_number,
            'subtype' => 'WALLET',
        ],
        'payment_token' => $payment_token,
    ]);

    $walletData = $walletResponse->json();
    $url = $walletData['iframe_redirection_url']
        ?? $walletData['iframe_url']
        ?? $walletData['redirection_url']
        ?? $walletData['redirect_url']
        ?? null;

    if (!$url) {
        return response()->json(['message' => 'Failed to get wallet payment URL', 'paymob_response' => $walletData], 500);
    }

    return response()->json([
        'message'         => 'تم إنشاء طلب التوثيق، أكمل الدفع',
        'verification_id' => $pendingVerification->id,
        'payment_url'     => $url,
        'amount'          => $amount,
    ]);
}
    public function processRefund($verificationRequest)
    {
        $token = $this->getPaymobToken();
        if (!$token) return false;

        $response = Http::post(env('PAYMOB_BASE_URL') . '/api/acceptance/void_refund/refund', [
            'auth_token'     => $token,
            'transaction_id' => $verificationRequest->transaction_id,
            'amount_cents'   => $verificationRequest->price * 100,
        ]);

        if ($response->successful()) {
            $verificationRequest->update(['payment_status' => 'refunded']);
            $verificationRequest->user->notify(new \App\Notifications\VerificationRefunded($verificationRequest));
            return true;
        }

        return false;
    }

    private function getPaymobToken()
    {
        return Http::post(env('PAYMOB_BASE_URL') . '/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ])->json()['token'] ?? null;
    }

    public function requestToVerify(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string',
            'role'            => 'required|in:coach,Seller,vendor',
            'documents'       => 'nullable|array',
            'documents.*'     => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method'  => 'required|in:visa,vodafone_cash',
            'phone_number'    => 'required_if:payment_method,vodafone_cash|string',
        ]);

        $exist = VerificationRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exist) {
            return response()->json(['message' => 'عندك طلب قيد المراجعة بالفعل'], 422);
        }

        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documentPaths[] = $file->store('verifications', 'public');
            }
        }

        $pendingVerification = PendingVerification::create([
            'user_id'        => auth()->id(),
            'role'           => $data['role'],
            'documents'      => $documentPaths,
            'payment_method' => $data['payment_method'],
            'phone_number'   => $request->phone_number,
            'price'          => 1250,
        ]);

        if ($data['payment_method'] == 'visa') {
            return $this->payWithVisaForVerification($request, $pendingVerification);
        }

        if ($data['payment_method'] == 'vodafone_cash') {
            return $this->payWithWalletForVerification($request, $pendingVerification, $request->phone_number);
        }
   
        }



  public function ledger(Request $request)
    {
        $user = auth()->user();

        $entries = LedgerEntry::where('account_type', get_class($user))
            ->where('account_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json($entries);
    }





    public function callback(Request $request)
{
    // بايموب بتبعت في الـ URL متغير اسمه success بـ true أو false
    $success = $request->query('success');
    
    if ($success === 'true') {
        return response()->json(['message' => 'Payment Successful! You can return to the app now.']);
    }

    return response()->json(['message' => 'Payment Failed or Canceled.']);
}


public function payWithVisaForBooking(Request $request, $booking)
{
    $user = auth()->user();
    $base = env("PAYMOB_BASE_URL");
    $amount_cents = $booking->total_price * 100;

    // 1. Auth Token
    $token = Http::post($base . '/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY')
    ])->json()['token'] ?? null;

    if (!$token) return response()->json(['message' => 'Paymob auth failed'], 500);

    // 2. Create Paymob Order (ونبعت الـ id كـ merchant_order_id)
    $paymobOrder = Http::post($base . '/api/ecommerce/orders', [
        'auth_token'        => $token,
        'delivery_needed'   => false,
        'amount_cents'      => $amount_cents,
        'currency'          => 'EGP',
        'merchant_order_id' => $booking->id, // ربط صريح
        'items'             => []
    ])->json();

    $paymob_order_id = $paymobOrder['id'] ?? null;
    if (!$paymob_order_id) return response()->json(['message' => 'Failed to create Paymob order'], 500);

    // 🔥 الحفظ السحري لـ paymob_order_id جوه جدول الحجوزات لتفادي الـ 404
    $booking->paymob_order_id = $paymob_order_id;
    $booking->save();

    // 3. Payment Key
    $response = Http::post($base . '/api/acceptance/payment_keys', [
        'auth_token'     => $token,
        'amount_cents'   => $amount_cents,
        'expiration'     => 3600,
        'order_id'       => $paymob_order_id,
        'currency'       => 'EGP',
        'integration_id' => env('PAYMOB_VISA_INTEGRATION_ID'),
        'billing_data'   => [
            'first_name'   => $user->name ?? 'User',
            'last_name'    => $user->name ?? 'Test',
            'email'        => $user->email ?? 'test@test.com',
            'phone_number' => $user->phone ?? '01000000000',
            'apartment' => 'NA', 'floor' => 'NA', 'street' => 'NA', 'building' => 'NA',
            'shipping_method' => 'NA', 'postal_code' => '12345', 'city' => 'Cairo', 'country' => 'EG', 'state' => 'Cairo',
        ]
    ]);

    $payment_token = $response->json()['token'] ?? null;
    if (!$payment_token) return response()->json(['message' => 'Failed to generate payment key'], 500);

    $url = 'https://accept.paymob.com/api/acceptance/iframes/' . env('PAYMOB_IFRAME_ID') . '?payment_token=' . $payment_token;
    return response()->json(['payment_url' => $url]);
}


        }