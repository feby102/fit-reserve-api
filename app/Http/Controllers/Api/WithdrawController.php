<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
public function request(Request $request)
{
    $data=$request->validate([

        'amount'=>'required|numeric|min:1',

        'bank_name'=>'nullable',

        'account_number'=>'nullable',

        'wallet_number'=>'nullable'

    ]);

    $user=auth()->user();

    if($user->wallet->balance < $data['amount']){

        return response()->json([
            'message'=>'Insufficient balance'
        ],422);

    }

    $withdraw=$user->withdrawRequests()->create($data);

    return response()->json($withdraw);
}







public function index()
{
    return WithdrawRequest::with('user')
        ->latest()
        ->get();
}
 



public function approve($id)
{
    $withdraw=WithdrawRequest::findOrFail($id);

    if($withdraw->status!='pending'){
        return response()->json([
            'message'=>'Already processed'
        ]);
    }

    DB::transaction(function() use($withdraw){

        $user=$withdraw->user;
app(\App\Services\WalletService::class)->debit(
    $user,
    $withdraw->amount,
    'withdraw',
    "Withdraw Request #{$withdraw->id}"
);
        $withdraw->update([
            'status'=>'approved'
        ]);

    });

    return response()->json([
        'message'=>'Approved'
    ]);
}
 


public function reject(Request $request,$id)
{
    $withdraw=WithdrawRequest::findOrFail($id);

    $withdraw->update([

        'status'=>'rejected',

        'reason'=>$request->reason

    ]);

    return response()->json([
        'message'=>'Rejected'
    ]);
}
 



public function myRequests()
{
    return auth()->user()
        ->withdrawRequests()
        ->latest()
        ->get();
}


}
