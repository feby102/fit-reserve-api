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
     Schema::create('ledger_entries', function (Blueprint $table) {
    $table->id();
    $table->morphs('account'); // account_type + account_id
    $table->string('type');
    $table->decimal('amount', 10, 2);
    $table->text('description')->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
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
