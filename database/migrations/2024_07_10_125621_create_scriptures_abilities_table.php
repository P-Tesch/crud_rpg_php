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
        Schema::create('scripture_abilities', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->string("description", 511);
            $table->integer("level");
            $table->integer("cost");
            $table->string("strategy")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scripture_abilities');
    }
};
