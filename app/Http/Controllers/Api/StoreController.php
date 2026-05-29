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
    $vendor = auth()->user()->vendor;
    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

     $stores = Store::where('vendor_id', $vendor->id)
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
        }
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
    $vendor = auth()->user()->vendor;
    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // عرض الستور مع الريفيوز عشان الفيندور يتابعها
    $store = Store::with(['products', 'reviews.user'])
        ->where('vendor_id', $vendor->id)
        ->findOrFail($id);

    $average = $store->reviews->avg('rating');

    return response()->json([
        'store' => $store,
        'average_rating' => round($average ?? 0, 1),
    ]);
}
 
 
public function store(Request $request)
{
    $vendor = auth()->user()->vendor;

    if (!$vendor) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'logo' => 'nullable|image',
    ]);

     if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('stores', 'public');
    }

    $store = Store::create([
        'vendor_id' => $vendor->id,
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        'logo' => $data['logo'] ?? null,
        'is_active' => true
    ]);

    return response()->json($store);
}




 
 public function update(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

    $store = Store::findOrFail($id);

     if ($store->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->validate([
        'name' => 'sometimes|string',
        'description' => 'nullable|string',
        'logo' => 'nullable|image',
        'is_active' => 'boolean'
    ]);

    // لو فيه صورة
    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('stores', 'public');
    }

    $store->update($data);

    return response()->json($store);
}
 public function destroy($id)
{
    $vendor = auth()->user()->vendor;

    $store = Store::findOrFail($id);

    if ($store->vendor_id != $vendor->id) {
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
