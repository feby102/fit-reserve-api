<?php

use App\Models\LedgerEntry;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\WalletService;

class CommissionService
{
   public function distribute($amount, User $seller)
{
    $settings = Setting::first();

    $rate = $settings->commission_rate ?? 10;

    $commission = ($amount * $rate) / 100;

    $sellerAmount = $amount - $commission;

    $walletService = app(WalletService::class);

    $admin = User::where('role','admin')->first();

    $walletService->credit(
        $admin,
        $amount,
        'credit',
        'Gross amount received'
    );

    $walletService->debit(
        $admin,
        $sellerAmount,
        'debit',
        "Transferred to vendor #{$seller->id}"
    );

    $walletService->credit(
        $seller,
        $sellerAmount,
        'credit',
        'Vendor payout'
    );

    LedgerEntry::create([
        'account_type' => get_class($admin),
        'account_id' => $admin->id,
        'type' => 'commission',
        'amount' => $commission,
        'description' => 'Platform commission',
    ]);
}
}