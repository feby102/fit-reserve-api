<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

 public function totaluser()
    {
$totalUser=User::all();
$usersActive=User::where('is_active',true)->count();
$totalUser=User::count();
return response()->json([
    'total_users' => $totalUser,
    'active_users' => $usersActive
]);
    }

 public function index(Request $request)
    {

     $query=User::query();

    if($request->has('role')){

       $query->where('role',$request->role);

    }


     if($request->has('city')){

      $query->where('city',$request->city);

    }


 if($request->has('is_active')){


$query->where('is_active',$request->is_active);
    }


    $user=$query->get();
        return \response()->json($user);
    }



//is_active


    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'User activated' : 'User deactivated',
            'user' => $user
        ]);
    }

    // (is_verified)
    public function verifyAccount($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'User verified successfully', 'user' => $user]);
    }





     public function show($id)
    {
        return User::with('wallet','academySubscriptions','privateCoachBookings')->findOrFail($id);
    }


    
     public function update(Request $request,$id)
    {
        $user=User::findOrFail($id);
$user->update($request->only(['name','email','city']));
        return response()->json(['message'=>'updated']);
    }

     public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message'=>'deleted']);
    }




 

    public function ranking()
{

return Ranking::with('user')->orderByDesc('points')->get();
}


  public function transfer(Request $request)
    {
        $from=auth()->user();
        $to=User::findOrFail($request->user_id);
        $amount=$request->amount;

        $fromWallet=$from->wallet;
        $toWallet=$to->wallet;

        if($fromWallet->balance<$amount){
            return response()->json(['message'=>'Insufficient balance'],400);
        }

        DB::transaction(function() use($fromWallet,$toWallet,$amount){
            $fromWallet->decrement('balance',$amount);
            $toWallet->increment('balance',$amount);
        });

        return response()->json(['message'=>'transfer done']);
    }





    public function usersGrowth()
{
$users=User::select(
DB::raw('MONTH(created_at) as mounth')
,
DB::raw('count(*) as total')
)->groupBy('mounth')->orderBy('mounth')->get();


return response()->json($users);


}
}
