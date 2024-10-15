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
            $table->string("strategy", 2047)->nullable();
            $table->string("strategy_burn", 2047)->nullable();


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
