<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\AcademyService;
use App\Models\Product;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // عرض كل الكوبونات
    public function index()
    {
        return Coupon::with(['services','products'])->get();
    }

    // عرض كوبونات الـ Vendor فقط
    public function vendorCoupons()
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Coupon::where('vendor_id', $vendor->id)
            ->with(['services','products'])
            ->get();
    }

    // إنشاء كوبون
    public function store(Request $request)
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percent,blogger,specific,general',
            'value' => 'nullable|numeric',
            'max_usage' => 'nullable|integer',
            'expires_at' => 'nullable|date',
            'services' => 'nullable|array',
            'products' => 'nullable|array',
        ]);

        // دمج vendor_id مع البيانات
        $couponData = array_merge($data, [
            'vendor_id' => $vendor->id
        ]);

        $coupon = Coupon::create($couponData);

         if(!empty($data['services'])){
            $validServices = AcademyService::whereIn('id', $data['services'])->pluck('id');
            $coupon->services()->sync($validServices);
        }

        if(!empty($data['products'])){
            $validProducts = Product::whereIn('id', $data['products'])->pluck('id');
            $coupon->products()->sync($validProducts);
        }

        return response()->json(['message' => 'Coupon created', 'coupon' => $coupon]);
    }

    // تحديث الكوبون
    public function update(Request $request, $id)
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $coupon = Coupon::where('vendor_id', $vendor->id)->findOrFail($id);

        $data = $request->validate([
            'code' => 'required|unique:coupons,code,'.$id,
            'type' => 'required|in:fixed,percent,blogger,specific,general',
            'value' => 'nullable|numeric',
            'max_usage' => 'nullable|integer',
            'expires_at' => 'nullable|date',
            'services' => 'nullable|array',
            'products' => 'nullable|array',
        ]);

        $coupon->update($data);

        if(isset($data['services'])){
            $validServices = AcademyService::whereIn('id', $data['services'])->pluck('id');
            $coupon->services()->sync($validServices);
        }

        if(isset($data['products'])){
            $validProducts = Product::whereIn('id', $data['products'])->pluck('id');
            $coupon->products()->sync($validProducts);
        }

        return response()->json(['message' => 'Coupon updated', 'coupon' => $coupon]);
    }

    // حذف الكوبون
    public function destroy($id)
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $coupon = Coupon::where('vendor_id', $vendor->id)->findOrFail($id);

        $coupon->services()->detach();
        $coupon->products()->detach();
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted']);
    }
}