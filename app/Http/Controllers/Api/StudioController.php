<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index()
    {
    $studios=Studio::all();
        return response()->json([
        'data' => $studios
    ]);

}

public function store(Request $request){
$data=$request->validate([

 'name'=>'required|string',
        'description'=>'required',
        'address'=>'required',
        'price_per_session'=>'required|numeric'

]);

$studio=Studio::create(
 [
       'name'=> $request->name  ,
        'description'=> $request->description ,
        'address'=> $request->address ,
        'price_per_session'=> $request->price_per_session

 ]   
);


}

 public function update(Request $request,$id){
    $studio=Studio::findOrFail($id);
 
    $studio->update($request->all());

    return response()->json([

        'message' => 'Updated'

    ]);


   }





   //delete



  public function destroy(Request $request, $id)
{
     $studio = Studio::findOrFail($id);
 
    $studio->delete();

    return response()->json([

        'message' => 'Deleted'

    ]);
}

}
