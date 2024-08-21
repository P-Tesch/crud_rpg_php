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
        Schema::create('sheet_subsystem', function (Blueprint $table) {
            $table->id();
            $table->integer("burn_duration")->nullable();

            $table->unsignedBigInteger("subsystem_id");
            $table->foreign("subsystem_id")->references("id")->on("subsystems");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_subsystem');
    }
};
