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
        Schema::create('scripture_scripture_ability', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("scripture_ability_id");
            $table->foreign("scripture_ability_id")->references("id")->on("scripture_abilities");

            $table->unsignedBigInteger("scripture_id");
            $table->foreign("scripture_id")->references("id")->on("scriptures");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scripture_scripture_ability');
    }
};
