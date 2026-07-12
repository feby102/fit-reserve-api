<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{  


 
public function publicIndex()
{
     $stores = Store::where('is_active', true)
        ->with(['vendor', 'products'])
        ->withAvg(['reviews' => fn($q) => $q->where('is_hidden', false)], 'rating')
        ->get();

     $stores->transform(function ($store) {
        $store->reviews_avg_rating = round($store->reviews_avg_rating ?? 0, 1);
        return $store;
    });

    return response()->json($stores);
}

public function index()
{
    $vendor = auth()->user();
    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

     $stores = Store::where('user_id', $vendor->id)
        ->withAvg('reviews', 'rating')
        ->latest()
        ->get();

    $stores->transform(function ($store) {
        $store->reviews_avg_rating = round($store->reviews_avg_rating ?? 0, 1);
        return $store;
    });

    return response()->json($stores);
}


 public function publicShow($id)
{
     $store = Store::with([
        'products.category',
        'reviews' => function ($q) {
            $q->where('is_hidden', false)->with('user:id,name');
        },'videos'
    ])->findOrFail($id);

    if (!$store->is_active) {
        return response()->json(['message' => 'Store is inactive'], 403);
    }

    $average = $store->reviews->avg('rating');

    return response()->json([
        'store' => $store,
        'average_rating' => round($average ?? 0, 1),
    ]);
}

// في ميثود show الخاصة بالفيندور
public function show($id)
{
    $vendor = auth()->user();
    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // عرض الستور مع الريفيوز عشان الفيندور يتابعها
    $store = Store::with(['products', 'reviews.user'])
        ->where('user_id', $vendor->id)
        ->findOrFail($id);

    $average = $store->reviews->avg('rating');

    return response()->json([
        'store' => $store,
        'average_rating' => round($average ?? 0, 1),
    ]);
}
 
 
public function store(Request $request)
{
    $user = auth()->user();

    if (!$user->role=='seller') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'logo' => 'nullable|image',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   

    ]);

     if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('stores', 'public');
    }

      if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stores', 'public');
            $data['image'] = $path;   
        }

    $store = Store::create([
        'user_id' => $user->id,
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        'logo' => $data['logo'] ?? null,
        'is_active' => true,
            'image' => $data['image'] ?? null,

        
    ]);

    return response()->json($store);
}




 public function update(Request $request, $id)
{
    $user = auth()->user();
    $store = Store::findOrFail($id);

    if ($store->user_id != $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'name' => 'sometimes|string',
        'description' => 'nullable|string',
        'image' => 'nullable|image', 
        'is_active' => 'boolean'
    ]);

    if ($request->hasFile('image')) {
         $data['image'] = $request->file('image')->store('stores', 'public');
    }

    $store->update($data);

    return response()->json([
        'store' => $store,
        'message' => 'done'
    ]);
}




 public function destroy($id)
{
    $user = auth()->user();

    $store = Store::findOrFail($id);

     if ($store->user_id != $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }


    $store->delete();

    return response()->json(['message' => 'Store deleted successfully']);
}
  
 
 
 
    public function toggleStatus($id)
    {
        $store = Store::findOrFail($id);
        $store->is_active = !$store->is_active;
        $store->save();

        return response()->json([
            'message' => 'Store status updated',
            'is_active' => $store->is_active
        ]);
    }

}
