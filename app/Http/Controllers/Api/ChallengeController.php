<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  
use App\Http\Controllers\Api\PaymentController;

class ChallengeController extends Controller
{
use AuthorizesRequests;

public function publicIndex()
{
    $challenges = Challenge::where('status', 'ongoing')
        ->with('academy')
        ->get();

    return response()->json($challenges);
}




public function publicShow($id)
{
    $challenge = Challenge::with('academy', 'participants')
        ->where('status', 'ongoing')
        ->findOrFail($id);

    return response()->json($challenge);
}


public function index()
{
    $vendor = auth()->user()->vendor;

    $challenges = Challenge::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->with('participants')->get();

    return response()->json($challenges);
}

public function show($id)
{
    $vendor = auth()->user()->vendor;

    $challenge = Challenge::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->with('participants')->findOrFail($id);

    return response()->json($challenge);
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required',
        'academy_id' => 'required|exists:academies,id',
        'max_players' => 'required|integer',
        'price' => 'required|numeric',
        'duration' => 'required|integer',
        'status' => 'required|in:upcoming,ongoing,completed'
    ]);

    $vendor = auth()->user()->vendor;

     $academy = Academy::findOrFail($data['academy_id']);

    if ($academy->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
     $challengeData = array_merge($data, [
            'vendor_id' => $vendor->id
        ]);


    $challenge = Challenge::create($challengeData);

    return response()->json($challenge);
}



public function update(Request $request, $id)
{
    $vendor = auth()->user()->vendor;

     $challenge = Challenge::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->findOrFail($id);

     $data = $request->validate([
        'title' => 'sometimes|string',
        'academy_id' => 'sometimes|exists:academies,id',
        'max_players' => 'sometimes|integer',
        'price' => 'sometimes|numeric',
        'duration' => 'sometimes|integer',
        'status' => 'sometimes|in:upcoming,ongoing,completed',
    ]);

     if (isset($data['academy_id'])) {
        $academy = Academy::findOrFail($data['academy_id']);
        if ($academy->vendor_id != $vendor->id) {
            return response()->json(['message' => 'Unauthorized to assign to this academy'], 403);
        }
    }

     $challenge->update($data);

    return response()->json([
        'message' => 'Challenge updated successfully',
        'challenge' => $challenge
    ]);
}




public function ongoingCallenge(){

$ongoing=Challenge::where('status','ongoing')->get();
return \response()->json($ongoing);
}


 


//accapt or reject
public function updateParticipant(Request $request, $id)
{
    $participant = ChallengeParticipant::findOrFail($id);
    $vendor = auth()->user()->vendor;

    $challenge = $participant->challenge;

    if ($challenge->academy->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'status' => 'required|in:accepted,rejected'
    ]);

    $participant->update(['status' => $request->status]);

    Notification::create([
        'user_id' => $participant->user_id,
        'title' => $request->status == 'accepted'
            ? 'You have been accepted'
            : 'Request rejected',
        'message' => $request->status == 'accepted'
            ? "Approved: " . $challenge->title
            : "Rejected: " . $challenge->title
    ]);

    return response()->json($participant);
}

//ban


public function banParticipant($id)
{
    $participant = ChallengeParticipant::findOrFail($id);
    $vendor = auth()->user()->vendor;

    if ($participant->challenge->academy->vendor_id != $vendor->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $participant->update(['is_banned' => true]);

    return response()->json($participant);
}



//total revenue

public function stats($challenge_id)
{
    $vendor = auth()->user()->vendor;

    $challenge = Challenge::whereHas('academy', function ($q) use ($vendor) {
        $q->where('vendor_id', $vendor->id);
    })->findOrFail($challenge_id);

    $totalParticipants = $challenge->participants()
        ->where('status', 'accepted')
        ->count();

    $revenue = $challenge->price * $totalParticipants;

    return response()->json([
        'total_participants' => $totalParticipants,
        'revenue' => $revenue
    ]);
}


public function status(Request $request, $id)
{ 
    $user = auth()->user();
    
    if (!$user || !$user->vendor) {
        return response()->json(['message' => 'Unauthorized: Vendor profile not found'], 403);
    }

    $vendorId = $user->vendor->id;

     $challenge = Challenge::whereHas('academy', function ($q) use ($vendorId) {
        $q->where('vendor_id', $vendorId);
    })->findOrFail($id);
    $data = $request->validate([
        'status' => 'required|in:upcoming,ongoing,completed'
    ]);

    $challenge->update($data);

    return response()->json([
        'message' => 'updated',
        'status' => $challenge->status
    ]);
}



 
public function payForChallenge(Request $request, $challenge_id)
{


    $data = $request->validate([
        'payment_method' => 'required|in:wallet,visa'
    ]);

    $user = auth()->user();
    $challenge = Challenge::findOrFail($challenge_id);
$vendor = $challenge->vendor;

    if (!in_array($challenge->status, ['ongoing', 'upcoming'])) {
        return response()->json(['message' => 'Challenge not available'], 400);
    }

    $alreadyJoined = ChallengeParticipant::where('challenge_id', $challenge_id)
        ->where('user_id', $user->id)
        ->exists();

    if ($alreadyJoined) {
        return response()->json(['message' => 'Already joined'], 400);
    }

    $currentCount = ChallengeParticipant::where('challenge_id', $challenge_id)
        ->where('status', 'accepted')
        ->count();

    if ($currentCount >= $challenge->max_players) {
        return response()->json(['message' => 'Challenge is full'], 400);
    }

      if ($data['payment_method'] == 'wallet') {

        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $challenge->price) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        DB::transaction(function () use ($wallet, $challenge, $user,$vendor) {

            $wallet->decrement('balance', $challenge->price);
$vendor->increment('balance', $challenge->price);

            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $challenge->price,
                'description' => 'Challenge payment #' . $challenge->id
            ]);

            ChallengeParticipant::create([
                'challenge_id' => $challenge->id,
                'user_id' => $user->id,
                'status' => 'accepted'
            ]);
        });

        return response()->json([
            'message' => 'Joined successfully using wallet'
        ]);
    }

      if ($data['payment_method'] == 'visa') {

        $paymentController = new PaymentController();

        return $paymentController->payWithVisa($request, $challenge->price);
    }
}



public function join(Request $request, $id)
{
    $challenge = Challenge::findOrFail($id);
    $user = auth()->user();

    if (!in_array($challenge->status, ['ongoing', 'upcoming'])) {
        return response()->json(['message' => 'The challenge is not available'], 400);
    }

    $currentCount = ChallengeParticipant::where('challenge_id', $id)
        ->where('status', 'accepted')
        ->count();

    if ($currentCount >= $challenge->max_players) {
        return response()->json(['message' => 'Challenge is full'], 400);
    }

    $alreadyJoined = ChallengeParticipant::where('challenge_id', $id)
        ->where('user_id', $user->id)
        ->exists();

    if ($alreadyJoined) {
        return response()->json(['message' => 'Already joined'], 400);
    }

      if ($challenge->price > 0) {
        return $this->payForChallenge($request, $id);
    }

      $participant = ChallengeParticipant::create([
        'challenge_id' => $id,
        'user_id' => $user->id,
        'status' => 'accepted'
    ]);

    return response()->json([
        'message' => 'Joined successfully',
        'data' => $participant
    ]);
}
}