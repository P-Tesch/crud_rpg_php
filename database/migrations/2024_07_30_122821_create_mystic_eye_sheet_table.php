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
        Schema::create('mystic_eye_sheet', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("mystic_eye_id");
            $table->foreign("mystic_eye_id")->references("id")->on("mystic_eyes");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");

            $table->integer("current_cooldown");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mystic_eyes_sheets');
    }
};
