<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BannerController extends Controller
{
        public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Banner::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  
            'link'  => 'nullable|url',
        ]);

        // رفع الصورة وتخزين المسار
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner = Banner::create($data);

        return response()->json([
            'message' => 'Banner created successfully',
            'banner'  => $banner
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link'  => 'sometimes|nullable|url',
        ]);

         if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return response()->json([
            'message' => 'Banner updated successfully',
            'banner'  => $banner
        ]);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

         if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully']);
    }
}
