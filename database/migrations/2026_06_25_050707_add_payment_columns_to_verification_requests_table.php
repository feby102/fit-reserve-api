<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('verification_requests', function (Blueprint $table) {
 $table->string('payment_method')->nullable();   // visa, vodafone_cash
        $table->string('payment_status')->default('unpaid'); // unpaid, paid
        $table->string('paymob_order_id')->nullable();
        $table->string('transaction_id')->nullable();
        $table->decimal('price', 10, 2)->default(1250);
        $table->string('phone_number')->nullable(); // لو فودافون كاش
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            //
        });
    }
};
