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
        Schema::create('coach_bookings', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('private_coach_id')->constrained()->cascadeOnDelete();
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->integer('hours');
    $table->decimal('total_price',10,2);
    $table->enum('status',['confirmed','cancelled'])
        ->default('confirmed');

    $table->timestamps();

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_bookings');
    }
};
