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
        Schema::create('sheet_sonata', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("sonata_id");
            $table->foreign("sonata_id")->references("id")->on("sonatas");

            $table->unsignedBigInteger("sheet_id");
            $table->foreign("sheet_id")->references("id")->on("sheets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_sonata');
    }
};
