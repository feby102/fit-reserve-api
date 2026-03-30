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
    Schema::table('challenges', function (Blueprint $table) {
        $table->foreign('vendor_id')->nullable()
              ->references('id')
              ->on('vendors')
              ->cascadeOnDelete();
    });
}
public function down(): void
{
    Schema::table('challenges', function (Blueprint $table) {
        $table->dropForeign(['vendor_id']);
        $table->dropColumn('vendor_id');
    });
}
};
