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
        Schema::create('subsystems', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description", 2047);

            $table->unsignedBigInteger("passive_strategy_id")->nullable();
            $table->foreign("passive_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("active_strategy_id")->nullable();
            $table->foreign("active_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("burn_strategy_id")->nullable();
            $table->foreign("burn_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("system_id");
            $table->foreign("system_id")->references("id")->on("systems");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsystems');
    }
};
