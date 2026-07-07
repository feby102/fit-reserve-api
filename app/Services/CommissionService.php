<?php
namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\WalletService;
use Log;

class CommissionService
{
    public function distribute($amount, User $seller, $orderId)
    {Log::info('Commission Service Started');
        try {

            $settings = Setting::first();

            $rate = $settings->commission_rate ?? 10;

            $commission = ($amount * $rate) / 100;

            $sellerAmount = $amount - $commission;

            $walletService = app(WalletService::class);

            $admin = User::where('role', 'admin')->first();

            $walletService->credit(
                $admin,
                $amount,
                'credit',
                "Order #{$orderId} - gross amount received from Paymob"
            );

            Log::info('Commission Step 1');

            $walletService->debit(
                $admin,
                $sellerAmount,
                'debit',
                "Order #{$orderId} - transferred to vendor #{$seller->id}"
            );

            Log::info('Commission Step 2');

            $walletService->credit(
                $seller,
                $sellerAmount,
                'credit',
                "Order #{$orderId} item payout"
            );

            Log::info('Commission Step 3');

            LedgerEntry::create([
                'account_type' => get_class($admin),
                'account_id'   => $admin->id,
                'type'         => 'commission',
                'amount'       => $commission,
                'description'  => "Order #{$orderId} platform commission",
            ]);

            Log::info('Commission Step 4');

        } catch (\Throwable $e) {

            Log::error($e->getMessage());
            Log::error($e->getFile());
            Log::error($e->getLine());

            throw $e;
        }
    }
}