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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string("key");
            $table->integer("value");

            $table->unsignedBigInteger("sheet_id")->nullable();
            $table->foreign("sheet_id")->references("id")->on("sheets");

            $table->unsignedBigInteger("blood_id")->nullable();
            $table->foreign("blood_id")->references("id")->on("bloods");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
