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
        Schema::create('sonata_sonata_ability', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("sonata_ability_id");
            $table->foreign("sonata_ability_id")->references("id")->on("sonata_abilities");

            $table->unsignedBigInteger("sonata_id");
            $table->foreign("sonata_id")->references("id")->on("sonatas");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sonata_sonata_ability');
    }
};
