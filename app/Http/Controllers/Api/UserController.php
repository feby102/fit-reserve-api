<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingVerification;
use App\Models\Ranking;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http; // تم إضافة الـ Facade الخاص بـ Http هنا

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

    public function showVerifyRequest(Request $request)
    {
        $requests = VerificationRequest::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return response()->json($requests);
    }

    public function requestToVerify(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string',
            'role'            => 'required|in:coach,Seller,vendor',
            'documents'       => 'nullable|array',
            'documents.*'     => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method'  => 'required|in:visa,vodafone_cash',
        'phone_number'   => 'required_if:payment_method,vodafone_cash|string',
        ]);

        // ما يبعتش أكتر من طلب
        $exist = VerificationRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exist) {
            return response()->json([
                'message' => 'عندك طلب قيد المراجعة بالفعل'
            ], 422);
        }

        // رفع الوثائق
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documentPaths[] = $file->store('verifications', 'public');
            }
        }

        // حفظ الطلب
       $pendingVerification = PendingVerification::create([
    'user_id'        => auth()->id(),
    'role'           => $data['role'],
    'documents'      => $documentPaths,
    'payment_method' => $data['payment_method'],
    'phone_number'   => $request->phone_number,
    'price'          => 1250,
]);

        // روح على الدفع مباشرة
        $paymentController = new \App\Http\Controllers\Api\PaymentController();

        if ($data['payment_method'] == 'visa') {
           return $paymentController->payWithVisaForVerification(
    $request,
    $pendingVerification
);



            
        }

        if ($data['payment_method'] == 'vodafone_cash') {
            return $paymentController->payWithWalletForVerification(
                $request,
    $pendingVerification
,
                $request->phone_number
            );
        }
    }

    // توثيق الحسابات
    public function approve($id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);

        $verificationRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $verificationRequest->user->update([
            'role' => $verificationRequest->role
        ]);

        return response()->json(['message' => 'تم قبول الطلب']);
    }

    // ارفض طلب
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $verificationRequest = VerificationRequest::findOrFail($id);

        // متقدرش ترفض طلب اتوافق عليه قبل كده
        if ($verificationRequest->status === 'approved') {
            return response()->json(['message' => 'الطلب اتوافق عليه مش ممكن ترفضه'], 422);
        }

        $verificationRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        // لو دفع → رجّعله فلوسه
        if (
            $verificationRequest->payment_status === 'paid' &&
            $verificationRequest->transaction_id
        ) {
            $this->processRefund($verificationRequest);
        }

        return response()->json([
            'message' => 'تم رفض الطلب' . ($verificationRequest->refresh()->payment_status === 'refunded' ? ' وجاري إرجاع الفلوس' : '')
        ]);
    }

    // تحويل الكود المتكرر العشوائي إلى الدالة الصحيحة المسؤولة عن الارتجاع
    private function processRefund($verificationRequest)
    {
        Http::withHeaders(['Content-Type' => 'application/json'])
            ->post(env('PAYMOB_BASE_URL') . '/api/acceptance/void_refund/refund', [
                'auth_token'     => $this->getPaymobToken(),
                'transaction_id' => $verificationRequest->transaction_id,
                'amount_cents'   => $verificationRequest->price * 100,
            ]);

        $verificationRequest->update(['payment_status' => 'refunded']);
    }

    // helper صغير لـ Paymob
    private function getPaymobToken()
    {
        return Http::post(env('PAYMOB_BASE_URL') . '/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ])->json()['token'] ?? null;
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

    // معدل نمو المستخدمين شهرياً
    public function usersGrowth()
    {
        $users = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )->groupBy('month')->orderBy('month')->get();

        return response()->json($users);
    }
}