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
        Schema::create('miracle_sheet', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("miracle_id");
            $table->foreign("miracle_id")->references("id")->on("miracles");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miracle_sheet');
    }
};
