<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
 
public function index()
{

$products=Product::with('vendor','category')->get();

return response()->json($products);
}


 
public function vendorProducts()
{
    $vendor = auth()->user()->vendor;
    $products = Product::where('vendor_id', $vendor->id)->with('category')->get();
    return response()->json($products);
}



 public function store(Request $request)
{

$data=$request->validate([
    'name'=>'required|string',
          'description'=>'required|string|max:255'  ,
          'price'=>'required|numeric', 
          'discount'=>'required|numeric' ,
         'image' =>'nullable' ,
         'store_id'=>'required|exists:stores,id',
          'category_id'=>'required|exists:categories,id'
]);

        $vendor = auth()->user()->vendor;  


    $image_path = null;  



if($request->hasFile('image')){
    
$folder='product_image';
$image_path=$request->file('image')->store($folder,'public');
return $image_path;
}
$product = Product::create( [
     'vendor_id' => $vendor->id,
    'name'=>$data['name'],
          'description'=>$data['description']  ,
          'price'=>$data['price'],
          'discount'=>$data['discount'],
         'image' =>$image_path ,
          'category_id'=>$data['category_id'],
          'store_id'=>$data['store_id']
]);

return response()->json([
'message' => 'Product created successfully',

'data' => $product

]);

}




public function update(Request $request,$id)
{

 $vendor = auth()->user()->vendor;

    $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

    if ($request->hasFile('image')) {
        $folder = 'product_image';
        $data['image'] = $request->file('image')->store($folder, 'public');
    }
 $product->update($request->all());

return response()->json($product);

}



 public function destroy($id)
{

   $vendor = auth()->user()->vendor;

    $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

$product->delete();

return response()->json([

'status' => true,

'message' => 'Product deleted successfully'

]);

}

}


