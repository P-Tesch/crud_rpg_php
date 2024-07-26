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
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->string("description");
            $table->integer("remaining_turns")->nullable();
            $table->string("strategy")->nullable();

            $table->unsignedBigInteger("sheet_id")->nullable();
            $table->foreign("sheet_id")->references("id")->on("sheets");

            $table->unsignedBigInteger("item_id")->nullable();
            $table->foreign("item_id")->references("id")->on("items");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
