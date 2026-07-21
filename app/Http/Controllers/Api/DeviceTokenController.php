<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request){

$data=$request->validate([

'title'=>'required|string',
'device_type'=>'required|in:android,ios,web'

]);

DeviceToken::updateOrCreate([

'user_id'=>\auth()->id
, 'device_type'=>$data['device_type']??null]
,[ 'token'=>$data['token']]




);

return response()->json(['message' => 'Token saved']);
    }


  public function destroy(Request $request)
    {
         $request->validate(['token' => 'required|string']);

        DeviceToken::where('token', $request->token)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['message' => 'Token removed']);
    }

}
