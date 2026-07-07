<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{


   public function credit($user, $amount, $type, $description)
{
    $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

    DB::transaction(function () use ($wallet, $user, $amount, $type, $description) {

        $wallet->increment('balance', $amount);

        $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'status' => 'confirmed'
        ]);

        LedgerEntry::create([
            'account_type' => get_class($user),
            'account_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'description' => $description
        ]);
    });
}
  public function debit(User $user, $amount, $type, $description)
{
    \Log::info('Debit 1');

    $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

    \Log::info('Debit 2', [
        'balance' => $wallet->balance,
        'amount' => $amount,
    ]);

    if ($wallet->balance < $amount) {
        \Log::info('Debit 3');
        throw new Exception('Insufficient balance');
    }

    \Log::info('Debit 4');

    DB::transaction(function () use ($wallet, $user, $amount, $type, $description) {

        \Log::info('Debit 5');

        $wallet->decrement('balance', $amount);

        \Log::info('Debit 6');

        $wallet->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'status' => 'confirmed'
        ]);

        \Log::info('Debit 7');

        LedgerEntry::create([
            'account_type' => get_class($user),
            'account_id' => $user->id,
            'type' => $type,
            'amount' => -$amount,
            'description' => $description
        ]);

        \Log::info('Debit 8');
    });

    \Log::info('Debit 9');
}
    }