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
            $table->string("strategy", 2047)->nullable();

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
