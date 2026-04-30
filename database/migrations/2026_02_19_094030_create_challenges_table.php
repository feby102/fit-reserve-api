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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
             $table->string('title');
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->foreignId('academy_id')->constrained()->cascadeOnDelete();
    $table->integer('max_players');
    $table->decimal('price',10,2);
    $table->integer('duration');  
    $table->enum('status',['upcoming','ongoing','completed'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
