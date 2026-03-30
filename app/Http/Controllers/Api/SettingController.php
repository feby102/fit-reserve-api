<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
 

// show settings

public function index()
{

return Setting::first();

}


 
 public function update(Request $request)
    {
       $data = $request->validate([
        'commission_rate' => 'nullable|numeric|min:0|max:100',
        'cancellation_policy' => 'nullable|string',
        'is_store_enabled' => 'nullable|in:0,1,true,false', // تعديل بسيط لضمان القبول
        'is_challenges_enabled' => 'nullable|in:0,1,true,false',
        'is_videos_enabled' => 'nullable|in:0,1,true,false',
        'terms' => 'nullable|string',
        'privacy_policy' => 'nullable|string',
        'about_us' => 'nullable|string',
        'banner' => 'nullable|image'
    ]);

     if ($request->hasFile('banner')) {
        $data['banner'] = $request->file('banner')->store('banners', 'public');
    }
     $setting = Setting::updateOrCreate([], $data);

    return response()->json([
        'message' => 'Settings updated successfully',
        'data' => $setting
    ]);

}

}

