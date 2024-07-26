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
        Schema::create('school_spell', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("spell_id");
            $table->foreign("spell_id")->references("id")->on("spells");

            $table->unsignedBigInteger("school_id");
            $table->foreign("school_id")->references("id")->on("schools");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_spell');
    }
};
