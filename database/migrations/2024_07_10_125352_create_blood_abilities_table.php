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
        Schema::create('blood_abilities', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->text("description");

            $table->unsignedBigInteger("strategy_id")->nullable();
            $table->foreign("strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("blood_id");
            $table->foreign("blood_id")->references("id")->on("bloods");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_abilities');
    }
};
