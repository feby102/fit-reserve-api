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
        Schema::create('pending_payments', function (Blueprint $table) {
            $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('type');
    // verification
    // booking
    // order
    // academy_subscription
    // gym_subscription

    $table->unsignedBigInteger('reference_id')->nullable();

    $table->decimal('amount',10,2);

    $table->string('paymob_order_id')->nullable();

    $table->enum('status',[
        'pending',
        'paid',
        'failed'
    ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_payments');
    }
};
