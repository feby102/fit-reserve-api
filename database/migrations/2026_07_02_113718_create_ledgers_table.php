<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            $table->morphs('account'); 
    // user / vendor / admin

    $table->string('type'); 
    // credit | debit | commission | refund | booking | order

    $table->decimal('amount', 10, 2);

    $table->text('description')->nullable();

    $table->unsignedBigInteger('reference_id')->nullable();
    // booking_id / order_id / transaction_id

    $table->string('reference_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
