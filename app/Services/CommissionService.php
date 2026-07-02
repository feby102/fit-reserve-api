<?php
namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\Setting;

class CommissionService
{
    public function distribute($amount, $vendor)
    {
        $settings = Setting::first();
        $rate = $settings->commission_rate ?? 0;

        $commission = ($amount * $rate) / 100;
        $vendorProfit = $amount - $commission;

        // ربح vendor
        $vendor->increment('balance', $vendorProfit);

        // عمولة الأدمن
        $settings->increment('total_admin_commissions', $commission);
    
LedgerEntry::create([
    'account_type' => get_class($vendor),
    'account_id' => $vendor->id,
    'type' => 'commission',
    'amount' => $vendorProfit,
    'description' => 'Vendor profit'
]);

LedgerEntry::create([
    'account_type' => 'admin',
    'account_id' => 1,
    'type' => 'commission',
    'amount' => $commission,
    'description' => 'Platform commission'
]);


    }
    }