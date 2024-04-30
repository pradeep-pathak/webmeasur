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
        Schema::create('site_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('site_id');
            $table->index('site_id');
            $table->integer('total_visits');
            $table->integer('total_visits_today');
            $table->integer('total_visits_last_7_days');
            $table->integer('total_uniques');
            $table->float('bounce_rate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_stats');
    }
};
