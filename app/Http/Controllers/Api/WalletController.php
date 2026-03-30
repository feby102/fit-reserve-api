<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Http\Request;

class WalletController extends Controller
{
//show wallet

    public function show(Request $request){
$wallet = $request->user()->wallet()->with('transactions')->first();

return \response()->json($wallet);
    }



    //add money

    public function  deposit(Request $request)   {
    
    $data=$request->validate(['amount'=>'required|numeric|min:1']);
       $wallet=$request->user()->wallet;
       
       DB::transaction(function() use($data,$wallet){

$wallet->increment('balance',$data['amount']);



      $wallet->transactions()->create([
         
                'type' => 'credit',

                'amount' => $data['amount'],

                'description' => 'Deposit' ]);

       });
        return response()->json([

            'message' => 'Money added']);

       }



//withdraw

     public function  withdraw(Request $request){
         $data=$request->validate(['amount'=>'required|numeric|min:1']);
         
         $wallet=$request->user()->wallet;

if($wallet->balance<$data['amount']){
      return response()->json([

                'message' => 'Insufficient balance'

            ], 400);
}

         DB::transaction(function() use($data,$wallet){
         $wallet->decrement('balance',$data['amount']);
        $wallet->transactions()->create([
           'type'=>'debit',
           'amount'=>$data['amount'],
           'description' => 'Withdraw'

        ]); 

         });
  return response()->json([

            'message' => 'Money withdrawn'

        ]);
        }
    
    
    
    
 
    
    
    
    
        }
