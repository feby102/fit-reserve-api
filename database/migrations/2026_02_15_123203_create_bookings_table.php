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
        Schema::create('bookings', function (Blueprint $table) {
            
         $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
             $table->morphs('bookable');            
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('hours');
             $table->decimal('total_price', 10, 2);
            $table->string('payment_method')->default('cash'); // visa, cash, wallet
            $table->string('rejection_reason')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
             $table->string('full_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('parent_id_card')->nullable();  
            $table->string('personal_photo')->nullable();  
            
            // الكوبونات والنقاط
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};





 