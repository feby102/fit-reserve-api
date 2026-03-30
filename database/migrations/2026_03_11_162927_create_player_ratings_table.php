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
        Schema::create('player_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete(); // اللاعب اللي بيقيم
            $table->foreignId('rated_player_id')->constrained('users')->cascadeOnDelete(); // اللاعب اللي بيتم تقييمه
            $table->integer('rating')->unsigned();  
            $table->text('comment')->nullable();
                    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_ratings');
    }
};
