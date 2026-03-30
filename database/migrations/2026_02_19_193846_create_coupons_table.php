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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
             $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
             $table->string('code')->unique();
             $table->enum('type', ['fixed','percent','blogger','specific','general']);             
             $table->decimal('value',10,2)->nullable();             
             $table->integer('max_usage')->nullable();             
             $table->date('expires_at')->nullable();
            $table->foreignId('academy_service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
