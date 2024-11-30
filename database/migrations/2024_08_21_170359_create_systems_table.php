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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string("name", 63);
            $table->string("description", 2047);
            $table->integer("damage")->nullable();

            $table->unsignedBigInteger("passive_strategy_id")->nullable();
            $table->foreign("passive_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("active_strategy_id")->nullable();
            $table->foreign("active_strategy_id")->references("id")->on("strategies");

            $table->unsignedBigInteger("burn_strategy_id")->nullable();
            $table->foreign("burn_strategy_id")->references("id")->on("strategies");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
