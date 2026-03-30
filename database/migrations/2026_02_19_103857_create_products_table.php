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
        Schema::create('products', function (Blueprint $table) {

           $table->id();
           $table->string('name');
           $table->text('description')->nullable();
           $table->decimal('price',10,2);
           $table->decimal('discount',10,2)->default(0);
           $table->string('image')->nullable();
           $table->string('video')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
           $table->foreignId('store_id')->constrained()->cascadeOnDelete(); 

           $table->timestamps();
           });
           
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_products');
    }
};
