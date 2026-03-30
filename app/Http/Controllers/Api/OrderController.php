<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{


 

public function vendorOrders()
{
    $vendor = auth()->user()->vendor;

    $orders = Order::whereHas('items.product', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })
    ->with('items.product')
    ->get();

    return response()->json($orders);
}





public function store(Request $request)
{
    $data = $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    $user = auth()->user();

    $totalPrice = 0;

    // إنشاء الطلب
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 0,
        'status' => 'pending'
    ]);

    // إضافة المنتجات
    foreach ($data['items'] as $item) {

        $product = Product::findOrFail($item['product_id']);

        $price = $product->price - $product->discount;

        $totalPrice += $price * $item['quantity'];

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $price
        ]);
    }

     $order->update([
        'total_price' => $totalPrice
    ]);

    return response()->json([
        'message' => 'Order created',
        'data' => $order->load('items.product')
    ]);
}





 public function updateStatus(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

    $request->validate([
        'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
    ]);

    $order = Order::where('vendor_id', $vendor->id)->findOrFail($id);

    $order->update([
        'status' => $request->status
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Order status updated successfully',
        'data' => $order
    ]);
}




public function statistics()
{
    $vendor = auth()->user()->vendor;

    $totalSales = Order::where('vendor_id', $vendor->id)->sum('total_price');
    $totalOrders = Order::where('vendor_id', $vendor->id)->count();
    $totalProducts = Product::where('vendor_id', $vendor->id)->count();

    return response()->json([
        'status' => true,
        'data' => [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts
        ]
    ]);
}

}
