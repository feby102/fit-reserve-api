<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // جلب جميع المنتجات للعامة مع السيلر والـ Category
    public function index()
    {
        // تغيير الـ eager loading من vendor إلى seller
        $products = Product::with('seller', 'category')->get();

        return response()->json($products);
    }

    // جلب المنتجات الخاصة بالسيلر الحالي فقط
    public function sellerProducts()
    {
        $seller = auth()->user();
        
        // الفلترة بناءً على الـ seller_id
        $products = Product::where('seller_id', $seller->id)->with('category')->get();
        
        return response()->json($products);
    }

    // إضافة منتج جديد بواسطة السيلر
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric', 
            'discount' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id'
        ]);

        $seller = auth()->user();  
        $image_path = null;   

        // رفع الصورة إذا وجدت وتخزين المسار
        if ($request->hasFile('image')) {
            $folder = 'product_images'; // تعديل الاسم ليكون جمع وأوضح
            $image_path = $request->file('image')->store($folder, 'public');
        }

        // إنشاء المنتج وربطه بالـ seller_id
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'discount' => $data['discount'],
            'image' => $image_path,
            'category_id' => $data['category_id'],
            'store_id' => $data['store_id']
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    // تحديث منتج معين خاص بالسيلر الحالي
    public function update(Request $request, $id)
    {
        $seller = auth()->user();

        // التأكد أن المنتج يخص السيلر الحالي
        $product = Product::where('seller_id', $seller->id)->findOrFail($id);

        // عمل Validation مرن للتحديث
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric', 
            'discount' => 'sometimes|required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'store_id' => 'sometimes|required|exists:stores,id',
            'category_id' => 'sometimes|required|exists:categories,id'
        ]);

        // التعامل مع الصورة الجديدة وحذف القديمة لعدم تراكم الملفات
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $folder = 'product_images';
            $data['image'] = $request->file('image')->store($folder, 'public');
        }

        // تحديث المنتج بالبيانات المفحوصة فقط (أكثر أماناً من $request->all())
        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // حذف منتج خاص بالسيلر
    public function destroy($id)
    {
        $seller = auth()->user();

        $product = Product::where('seller_id', $seller->id)->findOrFail($id);

        // حذف صورة المنتج من السيرفر قبل حذف السجل
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}