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
        Schema::create('scriptures', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("creation_points");
            $table->integer("remaining_scripture_points");
            $table->integer("damage");
            $table->integer("range");
            $table->integer("sharpness");
            $table->boolean("double");
            $table->string("strategy")->nullable();
            $table->string("description");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scriptures');
    }
};
