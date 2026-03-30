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
        Schema::create('settings', function (Blueprint $table) {
        $table->id();
             $table->decimal('commission_rate', 5, 2)->nullable();
        $table->text('cancellation_policy')->nullable();

        $table->boolean('is_store_enabled')->default(false);
        $table->boolean('is_challenges_enabled')->default(false);
        $table->boolean('is_videos_enabled')->default(false);

        $table->text('terms')->nullable();
        $table->text('privacy_policy')->nullable();
        $table->text('about_us')->nullable();

        $table->string('banner')->nullable();
$table->decimal('total_admin_commissions', 15, 2)->default(0);
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
