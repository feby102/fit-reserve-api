<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\PlayerRating;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerRatingController extends Controller
{
    public function ratePlayer(Request $request,$challengeId){
$data=$request->validate([
            'rated_player_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
]);


$evaluatorId = auth()->id();
$challenge=Challenge::findOrFail($challengeId);


 if($challenge->status!=='completed' ){
return \response()->json(['message'=>'You cannot rate until the challenge is over']);
}
//تاكيد تواجد الاتنين  فى نفس التحدى
 
$participantsCount=ChallengeParticipant::where('challenge_id', $challengeId)
->whereIn('user_id',[$evaluatorId,$data[ 'rated_player_id']])->where('status','accepted')->count();
if ($participantsCount < 2) {
        return response()->json(['message' => 'Both players must be accepted participants in this challenge'], 403);
    }


$rating = PlayerRating::updateOrCreate(
            [
                'challenge_id' => $challengeId,
                'evaluator_id' => $evaluatorId,
                'rated_player_id' => $request->rated_player_id
            ],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );
return response()->json(['message' => 'Rating submitted successfully', 'data' => $rating]);
    


 

    }



    public function getPlayerRatings($userId){
$ratings = PlayerRating::where('rated_player_id', $userId)
        ->with('evaluator:id,name')  
        ->get();

    return response()->json([
        'ratings' => $ratings,
        'average_rating' => $ratings->avg('rating')  
    ]);

    }
}
