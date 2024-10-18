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
        Schema::create('effect_item', function (Blueprint $table) {
            $table->id();
            $table->integer("remaining_duration")->nullable();
            $table->integer("power")->nullable();

            $table->unsignedBigInteger("effect_id");
            $table->foreign("effect_id")->references("id")->on("effects");

            $table->unsignedBigInteger("item_id");
            $table->foreign("item_id")->references("id")->on("items");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effect_item');
    }
};
