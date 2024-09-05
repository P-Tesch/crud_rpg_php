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
        Schema::create('effect_sheet', function (Blueprint $table) {
            $table->id();
            $table->integer("remaining_duration");

            $table->unsignedBigInteger("effect_id");
            $table->foreign("effect_id")->references("id")->on("effects");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effect_sheet');
    }
};
