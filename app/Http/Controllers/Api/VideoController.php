<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoReport;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(){
$videos = Video::with(['user', 'academy', 'coach','stadium','store','gym'])
    ->where('status', 'approved')  
        ->latest()
    ->paginate(10);         
       return response()->json($videos);

    }





    
public function latestVideos()
{
    $videos = Video::with(['user', 'academy', 'coach','stadium','store','gym'])
        ->latest()  
        ->take(5)
        ->get();

    return response()->json([
        'data' => $videos
    ]);
}




public function store(Request $request)
    {
        $validator =$request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'url'          => 'required|url',  
            'type'         => 'required|in:user,academy,coach,stadium,store,gym',
            'academy_id'   => 'nullable|exists:academies,id',
            'coach_id'     => 'nullable|exists:private_coaches,id',
             'stadium_id'   => 'nullable|exists:stadiums,id',
            'gym_id'     => 'nullable|exists:gyms,id',
            'store_id'     => 'nullable|exists:stores,id',
            
       
        ]);

         
$video = Video::create([
    'title'       => $request->title,
    'description' => $request->description,
    'url'         => $request->url,
    'type'        => $request->type,
    'user_id'     => auth()->id(),  
    'academy_id'  => $request->academy_id,
    'coach_id'    => $request->coach_id,
    'stadium_id'  => $request->stadium_id,  
    'gym_id'      => $request->gym_id,
    'store_id'      => $request->store_id,
    'views'       => 0,
    'likes'       => 0,
    'dislikes'    => 0,
    'status'      => 'approved',   
]);
        return response()->json([
            'message' => 'Video uploaded successfully',
            'data'    => $video->load(['academy', 'coach'])
        ], 201);
    }


    public function approve($id)
    {
       $video=Video::findOrFail($id);
       $video->status='approved';
        $video->save();

        return response()->json(['message'=>'Video approved']);
    }

     public function reject($id)
    {
        $video = Video::findOrFail($id);
        $video->status = 'rejected';
        $video->save();

        return response()->json(['message'=>'Video rejected']);
    }



public function destroy($id)
{
    $video = Video::where('user_id', auth()->id())->findOrFail($id);
    $video->delete();

    return response()->json(['message' => 'Video deleted successfully']);
}

 
     public function stats($id)
    {
        $video = Video::findOrFail($id);

        return response()->json([
            'views' => $video->views,
            'likes' => $video->likes,
            'dislikes' => $video->dislikes,
            'reports_count' => $video->reports()->count()
        ]);
    }



      public function reports($id)
    {
        $video = Video::with('reports.user')->findOrFail($id);
        return response()->json($video->reports);
    }



public function report(Request $request, $id)
{
     $video = Video::findOrFail($id);

     $request->validate([
        'reason' => 'required|string|min:5|max:500',
    ]);

     $alreadyReported = VideoReport::where('video_id', $id)
        ->where('user_id', auth()->id())
        ->exists();

    if ($alreadyReported) {
        return response()->json([
            'message' => 'you did it already!'
        ], 422);
    }

      
    $report = VideoReport::create([
        'video_id' => $video->id,
        'user_id'  => auth()->id(),
        'reason'   => $request->reason,
    ]);

    return response()->json([
        'message' => ' donet',
        'data'    => $report
    ], 201);
}

    }
