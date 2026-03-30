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
        Schema::create('stadium_packages', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->integer('hours');
            $table->decimal('price', 10, 2);
            $table->enum('type', ['weekly','monthly','3 month','6 month','1 year']);
 
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stadium_packages');
    }
};
