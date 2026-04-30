<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
 
   public function register(RegisterRequest $request)
{

    $data = $request->validated();

    $user = User::create([

        'name' => $data['name'],

        'email' => $data['email'],

'password' => bcrypt($data['password']),
         
        'city' => $data['city'] ?? null,

        'role' => $data['role'] ?? 'player',

        'is_active' => true,

        'is_verified' => false,

        'wallet_balance' => 0,
     'referral_code'=>Str::upper(Str::random(6))
    ]);

    if(!empty($data['referral_code'])){
        $referrer = User::where('referral_code', $data['referral_code'])->first();
        if($referrer){
             Referral::create([
                'referrer_id'=>$referrer->id,
                'referred_id'=>$user->id,
                'reward'=>50 
            ]);

             $referrer->wallet->increment('balance', 50);
        }
    }

 
    $token = $user->createToken('api-token')->plainTextToken;




    if($data['role']==='vendor'){
        Vendor::create([
            'name'=>$data['name'],
  'user_id'=>$user->id
        ]);
    }

    return response()->json([

        'message' => 'Registered successfully',

        'user' => $user,

        'token' => $token

    ]);

}

 public function login(Request $request)
{
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($data)) {
        return response()->json([
            'message' => 'Wrong credentials'
        ], 401);
    }

    $user = Auth::user();

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
}

public function forgot(Request $request)
{
 $data = $request->validate([
        'email' => 'required|email',   
        ]);
        $status=Password::sendResetLink($request->only('email'));

        return \response()->json([   
             'message' => __($status)
]);
}

public function reset(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'token' => 'required'
    ]);
    
    $status=Password::reset($request->only( 'email','password','password_confirmation' ,'token' ),
    function ($user,$password){
        $user->password=Hash::make($password);
        $user->save();

    }
    
    
    );
    
    
    return response()->json([
        'message' => __($status)
    ]);
    }


    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}
}