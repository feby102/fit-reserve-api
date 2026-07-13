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
        Schema::create('conversations', function (Blueprint $table) {
         $table->id();
        $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
        $table->string('title')->nullable(); // أضفنا حقل الـ title هنا وجعلناه اختيارياً
        $table->enum('status', ['open', 'closed'])->default('open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
