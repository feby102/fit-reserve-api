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
    $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

    \Log::info('Debit Wallet', [
        'user_id' => $user->id,
        'balance' => $wallet->balance,
        'amount' => $amount,
    ]);

    if ($wallet->balance < $amount) {
        \Log::error('Insufficient balance', [
            'balance' => $wallet->balance,
            'amount' => $amount,
        ]);

        throw new \Exception('Insufficient balance');
    }

    DB::transaction(function () use ($wallet, $user, $amount, $type, $description) {

        \Log::info('Before decrement');

        $wallet->decrement('balance', $amount);

        \Log::info('After decrement');

        $wallet->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'status' => 'confirmed'
        ]);

        \Log::info('Transaction created');

        LedgerEntry::create([
            'account_type' => get_class($user),
            'account_id' => $user->id,
            'type' => $type,
            'amount' => -$amount,
            'description' => $description
        ]);

        \Log::info('Ledger created');
    });
}
    }