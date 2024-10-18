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
        Schema::create('sonata_abilities', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->string("description", 2047);
            $table->integer("level");
            $table->string("cost", 63);
            $table->unsignedBigInteger("sonata_id");

            $table->unsignedBigInteger("strategy_id")->nullable();
            $table->foreign("strategy_id")->references("id")->on("strategies");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sonata_abilities');
    }
};
