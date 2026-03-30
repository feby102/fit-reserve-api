<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class CartController extends Controller
{
public function index()
{
    $user = auth()->user();

    $cart = Cart::where('user_id', $user->id)
        ->with('product')
        ->get();

    return response()->json($cart);
}



 public function add(Request $request)
{
    $data = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1'
    ]);

    $user = auth()->user();

    $cartItem = Cart::where('user_id', $user->id)
        ->where('product_id', $data['product_id'])
        ->first();

    if ($cartItem) {
         $cartItem->increment('quantity', $data['quantity']);
    } else {
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity']
        ]);
    }

    return response()->json(['message' => 'Added to cart']);
}



 public function update(Request $request, $id)
{
    $data = $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $user = auth()->user();

    $cartItem = Cart::where('user_id', $user->id)->findOrFail($id);

    $cartItem->update([
        'quantity' => $data['quantity']
    ]);

    return response()->json($cartItem);
}


public function remove($id)
{
    $user = auth()->user();

    $cartItem = Cart::where('user_id', $user->id)
                    ->where('id', $id)
                    ->firstOrFail();

    $cartItem->delete();

    return response()->json(['message' => 'Removed']);
}


 public function clear()
{
    $user = auth()->user();

    Cart::where('user_id', $user->id)->delete();

    return response()->json(['message' => 'Cart cleared']);
}
 

public function checkout()
{
    $user = auth()->user();

    $cartItems = Cart::where('user_id', $user->id)
        ->with('product')
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    $totalPrice = 0;

    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 0,
        'status' => 'pending'
    ]);

    foreach ($cartItems as $item) {

        $price = $item->product->price - $item->product->discount;

        $totalPrice += $price * $item->quantity;

        $order->items()->create([
            'product_id' => $item->product->id,
            'quantity' => $item->quantity,
            'price' => $price
        ]);
    }

    $order->update([
        'total_price' => $totalPrice
    ]);

     Cart::where('user_id', $user->id)->delete();

    return response()->json([
        'message' => 'Order created successfully',
        'data' => $order->load('items.product')
    ]);
}}
