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
        Schema::create('effect_system', function (Blueprint $table) {
            $table->id();
            $table->integer("remaining_duration")->nullable();
            $table->integer("power")->nullable();

            $table->unsignedBigInteger("effect_id");
            $table->foreign("effect_id")->references("id")->on("effects");

            $table->unsignedBigInteger("system_id");
            $table->foreign("system_id")->references("id")->on("systems");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effect_system');
    }
};
