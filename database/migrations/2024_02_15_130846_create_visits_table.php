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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("site_id");
            $table->index('site_id');
            $table->string("user_signature");
            $table->string('entry_page');
            $table->integer('duration')->nullable();
            $table->string("referrer")->nullable();
            $table->string("device")->nullable();
            $table->string("browser")->nullable();
            $table->string("os")->nullable();
            $table->string("country");
            $table->string("country_code");
            $table->string("region");
            $table->string("city");

            $table->timestamp('visited_at')->useCurrent();

            $table->timestamps();

            $table->foreign("site_id")->references("id")->on("sites")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
