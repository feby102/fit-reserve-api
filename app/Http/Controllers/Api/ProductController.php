<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
     public function index()
    {    $products = Product::with('seller', 'category')->get();

        return response()->json($products);
    }

    public function publicShow($id){
$product=Product::findOrFail($id);
dd($product);
return \response()->json($product);

    }

     public function sellerProducts()
    {
        $seller = auth()->user();
         $products = Product::where('seller_id', $seller->id)->with('category')->get();
        
        return response()->json($products);
    }

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

         if ($request->hasFile('image')) {
            $folder = 'product_images';  
                        $image_path = $request->file('image')->store($folder, 'public');
        }

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

     public function update(Request $request, $id)
    {
        $seller = auth()->user();

         $product = Product::where('seller_id', $seller->id)->findOrFail($id);

         $data = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric', 
            'discount' => 'sometimes|required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'store_id' => 'sometimes|required|exists:stores,id',
            'category_id' => 'sometimes|required|exists:categories,id'
        ]);

         if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $folder = 'product_images';
            $data['image'] = $request->file('image')->store($folder, 'public');
        }

         $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

     public function destroy($id)
    {
        $seller = auth()->user();

        $product = Product::where('seller_id', $seller->id)->findOrFail($id);

         if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }


    public function filter(Request $request){
         $product=Product::query()
         ->when($request->category_id,fn($q)=>
         $q->byCategory($request->category_id))->paginate(10);
        
return \response()->json($product);

    }
}