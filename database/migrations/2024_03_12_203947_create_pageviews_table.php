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
        Schema::create('pageviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->index('session_id');
            $table->unsignedBigInteger('site_id');
            $table->string('path');
            $table->string('title')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('scroll_depth')->nullable();

            $table->timestamp('viewed_at')->useCurrent();

            $table->timestamps();

            $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pageviews');
    }
};
