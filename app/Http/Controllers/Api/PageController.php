<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
 
public function index()
    {
        $pages = Page::latest()->get();
        return response()->json([
            'status' => true,
            'data'   => $pages
        ], 200);
    }

     public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'type'    => 'required|string|in:about,terms,privacy,policy',  
        ]);

        $page = Page::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Page created successfully',
            'data'    => $page
        ], 201);
    }

     public function show($id)
    {
        $page = Page::find($id);
        
        if (!$page) {
            return response()->json(['status' => false, 'message' => 'Page not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $page], 200);
    }

     public function update(Request $request, $id)
    {
        $page = Page::find($id);

        if (!$page) {
            return response()->json(['status' => false, 'message' => 'Page not found'], 404);
        }

        $data = $request->validate([
            'title'   => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'type'    => 'sometimes|string|in:about,terms,privacy,policy',
        ]);

        $page->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Page updated successfully',
            'data'    => $page
        ], 200);
    }

     public function destroy($id)
    {
        $page = Page::find($id);

        if (!$page) {
            return response()->json(['status' => false, 'message' => 'Page not found'], 404);
        }

        $page->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Page deleted successfully'
        ], 200);
    }

}
