<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // جلب إحصائيات المستخدمين بدون تحميل البيانات بالكامل في الذاكرة
    public function totaluser()
    {
        $usersActive = User::where('is_active', true)->count();
        $totalUser = User::count();

        return response()->json([
            'total_users' => $totalUser,
            'active_users' => $usersActive
        ]);
    }

    // عرض وتصفية المستخدمين بناءً على دورهم، مدينتهم، أو حالتهم
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->get();
        return response()->json($users);
    }

    // تفعيل أو تعطيل حساب مستخدم
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'User activated' : 'User deactivated',
            'user' => $user
        ]);
    }

    // توثيق الحساب
    public function verifyAccount($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'User verified successfully', 
            'user' => $user
        ]);
    }

    // عرض الملف الشخصي للمستخدم الحالي مع علاقاته
    public function show()
    {
        $user = auth()->user();

        $user->load([
            'wallet',
            'academySubscriptions',
            'privateCoachBookings'
        ]);

        return response()->json($user);
    }

    // تحديث بيانات مستخدم معين
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // التحقق من البيانات المرسلة قبل التحديث لحماية قاعدة البيانات
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'city' => 'sometimes|required|string|max:255',
        ]);

        $user->update($data);
        return response()->json(['message' => 'updated']);
    }

    // حذف مستخدم
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'deleted']);
    }

    // الترتيب بناءً على النقاط
    public function ranking()
    {
        return Ranking::with('user')->orderByDesc('points')->get();
    }

    // تحويل الأموال بين محافظ المستخدمين بشكل آمن عبر الـ Transactions
    public function transfer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $from = auth()->user();
        $to = User::findOrFail($request->user_id);
        $amount = $request->amount;

        $fromWallet = $from->wallet;
        $toWallet = $to->wallet;

        if (!$fromWallet || $fromWallet->balance < $amount) {
            return response()->json(['message' => 'Insufficient balance or wallet not found'], 400);
        }

        DB::transaction(function() use ($fromWallet, $toWallet, $amount) {
            $fromWallet->decrement('balance', $amount);
            $toWallet->increment('balance', $amount);
        });

        return response()->json(['message' => 'transfer done']);
    }

    // معدل نمو المستخدمين شهرياً (تم تصحيح كلمة mounth إلى month)
    public function usersGrowth()
    {
        $users = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )->groupBy('month')->orderBy('month')->get();

        return response()->json($users);
    }
}