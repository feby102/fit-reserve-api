<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
 public function register(Request $request)
{
    $validatedData = $request->validate([
        'name'          => 'required|string|max:255',
        'phone'         => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        'city'          => 'required|string|max:255',
        'area'          => 'nullable|string|max:255',
        'password'      => 'required|string|min:8',
        'role'          => 'required|in:player,coach,vendor,seller,admin',
        'referral_code' => 'nullable|exists:users,my_referral_code',
        'email'         => 'required|email',
    ]);

    return DB::transaction(function () use ($validatedData) {

      if ($validatedData['role'] === 'vendor') {

    $vendor = Vendor::create([
        'name'     => $validatedData['name'],
        'phone'    => $validatedData['phone'],
        'city'     => $validatedData['city'],
        'area'     => $validatedData['area'],
        'email'    => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);

     $token = $vendor->createToken('vendor-token')->plainTextToken;

    return response()->json([
        'message' => 'Vendor registered successfully',
        'vendor'  => $vendor,
        'token'   => $token   
    ], 201);
}
         $referrer = null;

        if (!empty($validatedData['referral_code'])) {
            $referrer = User::where(
                'my_referral_code',
                $validatedData['referral_code']
            )->first();
        }

        $user = User::create([
            'name'             => $validatedData['name'],
            'phone'            => $validatedData['phone'],
            'city'             => $validatedData['city'],
            'area'             => $validatedData['area'],
            'password'         => Hash::make($validatedData['password']),
            'role'             => $validatedData['role'],
            'email'            => $validatedData['email'],
            'referred_by'      => $referrer ? $referrer->id : null,
            'my_referral_code' => Str::upper(Str::random(8)),
        ]);

        if ($referrer) {
            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
                'reward'      => 50
            ]);

            $referrer->increment('wallet_balance', 50);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'user'    => $user,
            'token'   => $token
        ], 201);
    });
}
    public function login(Request $request)
{
    $request->validate([
        'phone'    => 'required|string',
        'password' => 'required|string',
    ]);

    // ندور في users
    $user = User::where('phone', $request->phone)->first();

    if ($user && Hash::check($request->password, $user->password)) {

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'type'    => 'user',
            'user'    => $user,
            'token'   => $token
        ]);
    }

     $vendor = Vendor::where('phone', $request->phone)->first();

    if ($vendor && Hash::check($request->password, $vendor->password)) {

$token = $vendor->createToken('vendor-token')->plainTextToken;
        return response()->json([
            'message' => 'تم تسجيل دخول التاجر بنجاح',
            'type'    => 'vendor',
            'vendor'  => $vendor,
            'token'   => $token
        ]);
    }

    return response()->json([
        'message' => 'بيانات الدخول غير صحيحة'
    ], 401);
}

public function sendResetCode(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $email = $request->email;

    // البحث في الجدولين لمعرفة من صاحب الطلب
    $account = User::where('email', $email)->first() ?? Vendor::where('email', $email)->first();

    if (!$account) {
        return response()->json([
            'status'  => false,
            'message' => 'البريد الإلكتروني غير مسجل لدينا.'
        ], 442);
    }

    $code = rand(100000, 999999);

    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $email],
        [
            'token' => $code,
            'created_at' => now()
        ]
    );

     $account->notify(new \App\Notifications\ResetPasswordNotification($code));

    return response()->json([
        'status'  => true,
        'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني.'
    ], 200);
}
 public function reset(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
        'code'     => 'required'
    ]);

    $passwordReset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->code)
        ->first();

    if (!$passwordReset || now()->subMinutes(15)->gt($passwordReset->created_at)) {
        return response()->json([
            'status'  => false,
            'message' => 'الكود غير صحيح أو انتهت صلاحيته.'
        ], 422);
    }

    // البحث عن الحساب في الجدولين لتحديثه
    $account = User::where('email', $request->email)->first() ?? Vendor::where('email', $request->email)->first();

    if ($account) {
        $account->update([
            'password' => Hash::make($request->password)
        ]);
    }

    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json([
        'status'  => true,
        'message' => 'تم إعادة تعيين كلمة المرور بنجاح.'
    ], 200);
}
 public function logout(Request $request)
{
     $user = $request->user(); 

    if ($user) {
        $user->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    return response()->json([
        'message' => 'المستخدم غير مسجل دخول بالفعل'
    ], 401);
}
}