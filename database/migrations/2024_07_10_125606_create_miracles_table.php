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
        Schema::create('miracles', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->string("description", 511);
            $table->string("strategy")->nullable();
            $table->integer("cost");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miracles');
    }
};
