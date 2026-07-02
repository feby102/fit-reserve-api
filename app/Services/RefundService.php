<?php

n<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function refundBooking(Booking $booking)
    {
        DB::transaction(function () use ($booking) {

            if ($booking->refund_status == 'refunded') {
                return;
            }

            $user = $booking->user;

            app(WalletService::class)->deposit(
                $user,
                $booking->total_price,
                "Refund booking #{$booking->id}"
            );

            $booking->update([
                'refund_status' => 'refunded'
            ]);
        });
    }

    public function refundOrder(Order $order)
    {
        DB::transaction(function () use ($order) {

            if ($order->refund_status == 'refunded') {
                return;
            }

            $user = $order->user;

            app(WalletService::class)->deposit(
                $user,
                $order->total_price,
                "Refund order #{$order->id}"
            );

            $order->update([
                'refund_status' => 'refunded'
            ]);
        });
    }
}