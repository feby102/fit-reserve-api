<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // عرض الأقسام للعامة مع بيانات السيلر
    public function publicIndex()
    {
        // تغيير الـ eager loading من vendor إلى seller
        $categories = Category::with('seller')->get();

        return response()->json($categories);
    }

    // عرض قسم معين للعامة
    public function publicShow($id)
    {
        $category = Category::with('seller')->findOrFail($id);

        return response()->json($category);
    }

    // عرض الأقسام الخاصة بالسيلر المسجل حالياً فقط
    public function sellerIndex()
    {
        $seller = auth()->user();

        // الفلترة بناءً على الـ seller_id
        $categories = Category::where('seller_id', $seller->id)->get();

        return response()->json($categories);
    }

    // إضافة قسم جديد بواسطة السيلر
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $seller = auth()->user();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        // ربط القسم بمعرف السيلر الحالي
        $data['seller_id'] = $seller->id;

        $category = Category::create($data);

        return response()->json([
            'message' => 'created',
            'data' => $category
        ], 21);
    }

    // تحديث القسم الخاص بالسيلر
    public function update(Request $request, $id)
    {
        $seller = auth()->user();

        // التأكد أن القسم يخص السيلر الحالي أولاً
        $category = Category::where('seller_id', $seller->id)->findOrFail($id);

        // تعديل الـ Validation ليكون PUT/PATCH friendly (استخدام 'sometimes' أو جعل الاسم 'nullable')
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json([
            'message' => 'updated',
            'data' => $category
        ]);
    }

    // حذف قسم خاص بالسيلر
    public function destroy($id)
    {
        $seller = auth()->user();

        $category = Category::where('seller_id', $seller->id)->findOrFail($id);

        // يفضل حذف ملف الصورة من السيرفر عند حذف القسم نهائياً
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }
}