<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Review;
use App\Models\Stadium;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'reviewable_type' => ['required', 'string', 'in:stadium,academy,Privatecoach,gym,challenge,store'],
        'reviewable_id'   => ['required', 'integer'],
        'rating'          => ['required', 'integer', 'min:1', 'max:5'],
        'comment'         => ['nullable', 'string']
    ]);

    $user = $request->user();

    // يجب إضافة باقي الأنواع هنا لتجنب الـ abort
    $class = match ($request->reviewable_type) {
        'stadium'      => \App\Models\Stadium::class,
        'academy'      => \App\Models\Academy::class,
        'gym'          => \App\Models\Gym::class,
        'challenge'    => \App\Models\Challenge::class,
        'Privatecoach' => \App\Models\PrivateCoach::class,
        'store'        =>\App\Models\Store::class,  
                default        => abort(400, 'Invalid type')
    };

    $model = $class::findOrFail($request->reviewable_id);

    $review = $model->reviews()->create([
        'user_id' => $user->id,
        'rating' => $request->rating,
        'comment' => $request->comment,
        'is_hidden' => false
    ]);

    return response()->json($review);
}
 


 




 
public function hide($id)
{

$review = Review::findOrFail($id);
$review->is_hidden = true;
$review->save();
return response()->json(['message'=>'hidden']);

}




 
public function average($type,$id)
{

switch($type){

case 'academy':

$model = \App\Models\Academy::findOrFail($id);

break;

case 'coach':

$model = \App\Models\PrivateCoach::findOrFail($id);

break;

case 'stadium':

$model = \App\Models\Stadium::findOrFail($id);

break;

case 'gym':

$model = \App\Models\Gym::findOrFail($id);

break;



case 'challenge':

$model = \App\Models\Challenge::findOrFail($id);

break;
default:
        return response()->json(['message' => 'Invalid type'], 400);
}



$avg = $model->reviews()->where('is_hidden',false)->avg('rating');

return response()->json(['average_rating'=>$avg]);
}


}
