<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\PendingVerification;
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
            'auth_token' => $token,
            'delivery_needed' => false,
            'amount_cents' => $amount_cents,
            'currency' => "EGP",
            'items' => []
        ]);
        
        $paymobOrderData = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($paymobOrderData, 500);
        }

        $paymob_order_id = $paymobOrderData['id'];

        // تحديث الموديل المحلي الأصلي بنجاح دون Crash
        $localOrder->update([
            'payment_method' => 'visa',
            'paymob_order_id' => $paymob_order_id
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

        $payment_token = $paymentKeyResponse->json()['token'] ?? null;
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

        /* create order */
        $orderResponse = Http::post($base.'/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => false,
            'amount_cents' => $amount_cents,
            'currency' => "EGP",
            'items' => []
        ]);
        
        $paymobOrderData = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($paymobOrderData, 500);
        }

        $paymob_order_id = $paymobOrderData['id'];

        $localOrder->update([
            'payment_method' => 'vodafone_cash',
            'paymob_order_id' => $paymob_order_id
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

    // 3️⃣ الـ Webhook المطور والآمن 100% من بايموب لعدم تكرار الطلبات والتحويل التلقائي المعزز بالـ Logs
    public function webhook(Request $request)
    { 
        Log::info('Paymob Webhook Reached');
        
        $obj = $request->input('obj');
        if (!$obj) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $receivedHmac = $request->input('hmac');

        $boolToString = function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if ($value === 'true' || $value === 'false') {
                return $value;
            }
            return $value ?? '';
        };

       // قراءة المفتاح السري من الـ .env
$secret = env('PAYMOB_HMAC_SECRET'); 

// حساب الـ HMAC بناءً على البيانات القادمة من بايموب (الترتيب هنا مهم جداً حسب توثيق بايموب)
$hmacData = [
    $request->input('obj.amount_cents'),
    $request->input('obj.created_at'),
    $request->input('obj.currency'),
    $request->input('obj.error_occured'),
    $request->input('obj.has_parent_transaction'),
    $request->input('obj.id'),
    $request->input('obj.integration_id'),
    $request->input('obj.is_3d_secure'),
    $request->input('obj.is_auth'),
    $request->input('obj.is_capture'),
    $request->input('obj.is_voided'),
    $request->input('obj.order.id'),
    $request->input('obj.owner'),
    $request->input('obj.pending'),
    $request->input('obj.source_data.pan'),
    $request->input('obj.source_data.sub_type'),
    $request->input('obj.source_data.type'),
    $request->input('obj.success'),
];

$hmacString = implode('', $hmacData);
$calculated_hmac = hash_hmac('sha512', $hmacString, $secret);

// استقبال الـ hmac القادم في الـ URL من بايموب وتأمين عدم وجود null
$hmac = $request->query('hmac') ?? '';

// المقارنة الآمنة لمنع الـ TypeError
if (hash_equals($calculated_hmac, $hmac)) {
    // 🟢 مبروك الريكوست حقيقي 100%! نفذي مسح الـ Pending هنا
     Log::error('HMAC Mismatch!');
            return response()->json(['message' => 'Invalid HMAC'], 403);
        }

        $paymobOrderId = $obj['order']['id'];
        $isSuccess = ($obj['success'] === true || $obj['success'] === 'true');

        // حماية هامة: لو لم تكن العملية ناجحة تماماً لا تقم بإجراء تعديلات داتابيز
        if (!$isSuccess) {
            Log::info('Webhook received but transaction not successful yet for order: ' . $paymobOrderId);
            return response()->json(['message' => 'Transaction not successful, skipping.']);
        }

        // 1️⃣ فحص إذا كان أوردر عادي
// 🟢 السطر الجديد الصح:
$pending = \App\Models\PendingVerification::where('paymob_order_id', $paymobOrderId)->first();

if (!$pending) {
    Log::error("Pending verification not found for Paymob Order: " . $paymobOrderId);
    return response()->json(['message' => 'Order not found'], 404);
}        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'transaction_id' => $obj['id'],
            ]);
            return response()->json(['message' => 'Order updated successfully']);
        }

        // 2️⃣ فحص طلب توثيق مؤقت (PendingVerification) ونقله فوراً ومسحه
        $pending = PendingVerification::where('paymob_order_id', $paymobOrderId)->first();
        if ($pending) {
            Log::info('Processing verification conversion for paymob order: ' . $paymobOrderId);

            VerificationRequest::create([
                'user_id'         => $pending->user_id,
                'role'            => $pending->role,
                'documents'       => $pending->documents, 
                'payment_method'  => $pending->payment_method,
                'phone_number'    => $pending->phone_number,
                'price'           => $pending->price,
                'payment_status'  => 'paid',
                'status'          => 'pending', 
                'transaction_id'  => $obj['id'],
                'paymob_order_id' => $paymobOrderId
            ]);

            // مسح الريكوست المؤقت بنجاح من الداتابيز لمنع التكرار
            $pending->delete();

            Log::info('Pending verification deleted and permanent request stored successfully.');
            return response()->json(['message' => 'Verification request created and pending deleted']);
        }

        // 3️⃣ حماية مكررة لو وصل ويب هوك مكرر للتوثيق المحفوظ مسبقاً
        $verification = VerificationRequest::where('paymob_order_id', $paymobOrderId)->first();
        if ($verification) {
            $verification->update([
                'payment_status' => 'paid',
                'transaction_id' => $obj['id'],
            ]);
            return response()->json(['message' => 'Verification already processed']);
        }

        return response()->json(['message' => 'No matching records found']);
    }

    // دفع فيزا للتوثيق
    // 1️⃣ دفع فيزا للتوثيق - نسخة معدلة ومؤمنة
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
}