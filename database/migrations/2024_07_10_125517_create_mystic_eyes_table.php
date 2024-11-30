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
        Schema::create('mystic_eyes', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63)->unique();
            $table->string("passive", 511)->nullable();
            $table->string("active", 511)->nullable();
            $table->integer("cooldown")->nullable();
            $table->integer("cost");

            $table->unsignedBigInteger("passive_strategy_id")->nullable();
            $table->foreign("passive_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("active_strategy_id")->nullable();
            $table->foreign("active_strategy_id")->references("id")->on("strategies");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mystic_eyes');
    }
};
