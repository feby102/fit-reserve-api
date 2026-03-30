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
   


     public function updateStatus(Request $request,$id)
    {
        $request->validate([
            'status'=>'required|in:confirmed,refunded'
        ]);
$transaction=WalletTransaction::findOrFail($id); 
$transaction->status = $request->status;
        $transaction->save();

if($request->status == 'refunded'){
            $wallet = $transaction->wallet;
            if($transaction->type == 'credit'){
                $wallet->decrement('balance',$transaction->amount);
            }elseif($transaction->type == 'debit'){
                $wallet->increment('balance',$transaction->amount);
            }
        }
return \response()->json(['done']);
}



//لإحصائيات: الإيرادات حسب القسم


   public function stats($academy_id) {
$academy = Academy::with([ 'plans'])->findOrFail($academy_id);
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

    // تصدير PDF
    public function exportPDF()
    {
        $transactions = WalletTransaction::with('wallet.user')->get();
        $pdf = PDF::loadView('admin.payments.pdf',compact('transactions'));
        return $pdf->download('transactions.pdf');
    }

 
 
public function payWithvisa($request,$amount){

$base = env("PAYMOB_BASE_URL");



    //  تحويل المبلغ إلى سنتات
    $amount_cents = $amount * 100;

    if ($amount_cents < 10) {
        return response()->json([
            'message' => 'Minimum payment is 0.10 EGP'
        ], 400);
    }

/*   generate token */

$tokenResponse = Http::post($base.'/api/auth/tokens',[
'api_key'=>env('PAYMOB_API_KEY')
]);

$token = $tokenResponse->json()['token'] ?? null;

if(!$token){
    return response()->json($tokenResponse->json(),500);
}

/* create order */

$orderResponse = Http::post($base.'/api/ecommerce/orders',[
'auth_token'=>$token,
'delivery_needed'=>false,
 'amount_cents' => $amount_cents,

'currency'=>"EGP",
'items'=>[]
]);

$order = $orderResponse->json();

if(!$orderResponse->successful()){
    return response()->json($order,500);
}

$order_id = $order['id'];

/* 3️⃣ generate payment key */

$paymentKeyResponse = Http::post($base.'/api/acceptance/payment_keys',[
"auth_token"=>$token,
"amount_cents"=>$amount * 100,
"expiration"=>3600,
"order_id"=>$order_id,
"currency"=>"EGP",
"integration_id"=>env('PAYMOB_INTEGRATION_ID'),
"billing_data"=>[
"first_name"=>"Test",
"last_name"=>"User",
"email"=>"test@test.com",
"phone_number"=>"01000000000",
"apartment"=>"NA",
"floor"=>"NA",
"street"=>"NA",
"building"=>"NA",
"shipping_method"=>"NA",
"postal_code"=>"NA",
"city"=>"Cairo",
"country"=>"EG",
"state"=>"NA"
]
]);

$paymentKey = $paymentKeyResponse->json();

$payment_token = $paymentKey['token'];

/*  payment url */

$url = "https://accept.paymob.com/api/acceptance/iframes/".env('PAYMOB_IFRAME_ID')."?payment_token=".$payment_token;

return response()->json([
'payment_url'=>$url
]);


 }








 public function pay(Request $request, $order_id)
{
    $data = $request->validate([
        'payment_method' => 'required|in:wallet,visa'
    ]);

    $user = auth()->user();

    $order = Order::where('user_id', $user->id)->findOrFail($order_id);

    if ($order->payment_status == 'paid') {
        return response()->json(['message' => 'Already paid']);
    }

     if ($data['payment_method'] == 'wallet') {

        $wallet = $user->wallet;

        if ($wallet->balance < $order->total_price) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
        }

        DB::transaction(function () use ($wallet, $order) {

            $wallet->decrement('balance', $order->total_price);

            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $order->total_price,
                'description' => 'Order payment #' . $order->id
            ]);

            $order->update([
                'payment_method' => 'wallet',
                 'status' => 'confirmed'
            ]);
        });

        return response()->json([
            'message' => 'Paid successfully using wallet',
            'order' => $order
        ]);
    }

     if ($data['payment_method'] == 'visa') {

        $order->update([
            'payment_method' => 'visa'
        ]);

        return $this->payWithvisa($request, $order->total_price);
    }
}
}