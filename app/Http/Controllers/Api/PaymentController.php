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

    // 1️⃣ فانكشن الدفع بالفيزا (تم تحديث الـ Integration ID)
    public function payWithvisa($request, $amount)
    {
        $base = env("PAYMOB_BASE_URL");
        $amount_cents = $amount * 100;

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
        $order = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($order, 500);
        }

        $order_id = $order['id'];

        /* generate payment key */
        $paymentKeyResponse = Http::post($base.'/api/acceptance/payment_keys', [
            "auth_token" => $token,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $order_id,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_VISA_INTEGRATION_ID'), // استخدام مفتاح الفيزا
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

    // 2️⃣ فانكشن الدفع بفودافون كاش / المحافظ الإلكترونية الجديدة
    public function payWithWallet($request, $amount, $phone_number)
    {
        $base = env("PAYMOB_BASE_URL");
        $amount_cents = $amount * 100;

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
        $order = $orderResponse->json();

        if (!$orderResponse->successful()) {
            return response()->json($order, 500);
        }

        $order_id = $order['id'];

        /* generate payment key (باستخدام ID المحافظ) */
        $paymentKeyResponse = Http::post($base.'/api/acceptance/payment_keys', [
            "auth_token" => $token,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $order_id,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_WALLET_INTEGRATION_ID'), // الـ ID الجديد للمحافظ
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

        /* الخطوة الرابعة والأهم: طلب الـ Redirection URL للمحافظ */
        $walletResponse = Http::post($base.'/api/acceptance/void_refund/transactions/pay', [
            "source" => [
                "identifier" => $phone_number, // رقم فودافون كاش للعميل
                "subtype" => "WALLET"
            ],
            "payment_token" => $payment_token
        ]);

        $walletData = $walletResponse->json();

        // Paymob بترجع اللينك في خانة 'iframe_url' أو 'redirection_url' للمحافظ
        $url = $walletData['iframe_url'] ?? $walletData['redirection_url'] ?? null;

        if (!$url) {
            return response()->json(['message' => 'Failed to generate wallet payment url', 'error' => $walletData], 500);
        }

        return response()->json([
            'payment_url' => $url
        ]);
    }

    // 3️⃣ تحديث الميثود الأساسية للتحكم في الاختيارات
    public function pay(Request $request, $order_id)
    {
        // تم تغيير الـ Validation ليدعم الاختيارين: paymob_visa و paymob_wallet
        // وإضافة شرط وجود رقم تليفون لو اختار محفظة
        $data = $request->validate([
            'payment_method' => 'required|in:app_wallet,paymob_visa,paymob_wallet',
            'phone_number' => 'required_if:payment_method,paymob_wallet|string' 
        ]);

        $user = auth()->user();
        $order = Order::where('user_id', $user->id)->findOrFail($order_id);

        if ($order->payment_status == 'paid') {
            return response()->json(['message' => 'Already paid']);
        }

        // الاختيار القديم: الدفع بمحفظة التطبيق الداخلية (App Wallet)
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
                    'status' => 'confirmed'
                ]);
            ]);

            return response()->json([
                'message' => 'Paid successfully using wallet',
                'order' => $order
            ]);
        }

        // الاختيار الثاني: دفع فيزا بايموب
        if ($data['payment_method'] == 'paymob_visa') {
            $order->update([
                'payment_method' => 'paymob_visa'
            ]);

            return $this->payWithvisa($request, $order->total_price);
        }

        // الاختيار الثالث الجديد: دفع فودافون كاش بايموب
        if ($data['payment_method'] == 'paymob_wallet') {
            $order->update([
                'payment_method' => 'paymob_wallet'
            ]);

            return $this->payWithWallet($request, $order->total_price, $data['phone_number']);
        }
    }





public function webhook(Request $request)
{
    // ... نفس كود الـ HMAC بتاعك

    if ($obj['success'] === true) {
        $paymobOrderId = $obj['order']['id'];

        // شوفي هل ده payment لأوردر عادي
        $order = Order::where('paymob_order_id', $paymobOrderId)->first();
        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'transaction_id' => $obj['id'],
            ]);
        }

        // ولا payment لـ verification request
        $verification = VerificationRequest::where('paymob_order_id', $paymobOrderId)->first();
        if ($verification) {
            $verification->update([
                'payment_status' => 'paid',
                'transaction_id' => $obj['id'],
                // status لسه pending → الأدمن هيوافق بعدين
            ]);
        }
    }

    return response()->json(['message' => 'ok']);
}









// دفع فيزا للتوثيق
public function payWithVisaForVerification(Request $request, $verificationRequest)
{
    $user   = auth()->user();
    $amount = $verificationRequest->price;
    $base   = env("PAYMOB_BASE_URL");
    $amount_cents = $amount * 100;

    // 1. Token
    $token = Http::post($base . '/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY')
    ])->json()['token'] ?? null;

    if (!$token) {
        return response()->json(['message' => 'Paymob auth failed'], 500);
    }

    // 2. Order
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

    // احفظ الـ paymob_order_id عشان الـ webhook يلاقيه
    $verificationRequest->update(['paymob_order_id' => $paymob_order_id]);

    // 3. Payment Key
    $payment_token = Http::post($base . '/api/acceptance/payment_keys', [
        'auth_token'     => $token,
        'amount_cents'   => $amount_cents,
        'expiration'     => 3600,
        'order_id'       => $paymob_order_id,
        'currency'       => 'EGP',
        'integration_id' => env('PAYMOB_VISA_INTEGRATION_ID'),
        'billing_data'   => [
            'first_name'      => $user->name,
            'last_name'       => $user->name,
            'email'           => $user->email,
            'phone_number'    => $user->phone ?? '01000000000',
            'apartment'       => 'NA', 'floor'           => 'NA',
            'street'          => 'NA', 'building'        => 'NA',
            'shipping_method' => 'NA', 'postal_code'     => 'NA',
            'city'            => 'Cairo', 'country'      => 'EG',
            'state'           => 'NA',
        ]
    ])->json()['token'] ?? null;

    if (!$payment_token) {
        return response()->json(['message' => 'Failed to generate payment key'], 500);
    }

    $url = 'https://accept.paymob.com/api/acceptance/iframes/'
         . env('PAYMOB_IFRAME_ID')
         . '?payment_token=' . $payment_token;

    return response()->json([
        'message'            => 'تم إنشاء طلب التوثيق، أكمل الدفع',
        'verification_id'    => $verificationRequest->id,
        'payment_url'        => $url,
        'amount'             => $amount,
    ]);
}

// دفع فودافون كاش للتوثيق
public function payWithWalletForVerification(Request $request, $verificationRequest, $phone_number)
{
    $user   = auth()->user();
    $amount = $verificationRequest->price;
    $base   = env("PAYMOB_BASE_URL");
    $amount_cents = $amount * 100;

    // 1. Token
    $token = Http::post($base . '/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY')
    ])->json()['token'] ?? null;

    if (!$token) {
        return response()->json(['message' => 'Paymob auth failed'], 500);
    }

    // 2. Order
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

    $verificationRequest->update(['paymob_order_id' => $paymob_order_id]);

    // 3. Payment Key
    $payment_token = Http::post($base . '/api/acceptance/payment_keys', [
        'auth_token'     => $token,
        'amount_cents'   => $amount_cents,
        'expiration'     => 3600,
        'order_id'       => $paymob_order_id,
        'currency'       => 'EGP',
        'integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
        'billing_data'   => [
            'first_name'      => $user->name,
            'last_name'       => $user->name,
            'email'           => $user->email,
            'phone_number'    => $phone_number,
            'apartment'       => 'NA', 'floor'           => 'NA',
            'street'          => 'NA', 'building'        => 'NA',
            'shipping_method' => 'NA', 'postal_code'     => 'NA',
            'city'            => 'Cairo', 'country'      => 'EG',
            'state'           => 'NA',
        ]
    ])->json()['token'] ?? null;

    if (!$payment_token) {
        return response()->json(['message' => 'Failed to generate payment key'], 500);
    }

    // 4. Wallet redirect URL
    $walletData = Http::post($base . '/api/acceptance/pay', [
        'source' => [
            'identifier' => $phone_number,
            'subtype'    => 'WALLET'
        ],
        'payment_token' => $payment_token
    ])->json();

    $url = $walletData['iframe_url'] ?? $walletData['redirection_url'] ?? null;

    if (!$url) {
        return response()->json(['message' => 'Failed to get wallet payment URL'], 500);
    }

    return response()->json([
        'message'         => 'تم إنشاء طلب التوثيق، أكمل الدفع',
        'verification_id' => $verificationRequest->id,
        'payment_url'     => $url,
        'amount'          => $amount,
    ]);
}





public function processRefund($verificationRequest)
{
    $token = $this->getPaymobToken();

    if (!$token) return false;

    $response = Http::post(
        env('PAYMOB_BASE_URL') . '/api/acceptance/void_refund/refund',
        [
            'auth_token'     => $token,
            'transaction_id' => $verificationRequest->transaction_id,
            'amount_cents'   => $verificationRequest->price * 100,
        ]
    );

    if ($response->successful()) {
        $verificationRequest->update(['payment_status' => 'refunded']);
        
        // ابعت notification للـ user
        $verificationRequest->user->notify(
            new \App\Notifications\VerificationRefunded($verificationRequest)
        );

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


}