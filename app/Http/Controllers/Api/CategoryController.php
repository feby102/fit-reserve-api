<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
 
public function publicIndex()
{
    $categories = Category::with('vendor')->get();

    return response()->json($categories);
}
 



public function publicShow($id)
{
    $category = Category::with('vendor')->findOrFail($id);

    return response()->json($category);
}
  



public function vendorIndex()
{
    $vendor = auth()->user()->vendor;

    $categories = Category::where('vendor_id', $vendor->id)->get();

    return response()->json($categories);
}
 

public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $vendor = auth()->user()->vendor;

     if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('categories', 'public');
    }

    $data['vendor_id'] = $vendor->id;

    $category = Category::create($data);

    return response()->json([
        'message' => 'created',
        'data' => $category
    ]);
}
 
public function update(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

    $category = Category::where('vendor_id', $vendor->id)->findOrFail($id);

    $data = $request->validate([
        'name' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

     if ($request->hasFile('image')) {

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






public function destroy($id)
{
    $vendor = auth()->user()->vendor;

    $category = Category::where('vendor_id', $vendor->id)->findOrFail($id);

    $category->delete();

    return response()->json([
        'message' => 'deleted'
    ]);
}
}

