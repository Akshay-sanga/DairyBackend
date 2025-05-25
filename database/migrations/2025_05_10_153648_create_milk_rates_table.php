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
        Schema::create('milk_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('fat', 3, 1);
            $table->decimal('snf_7_5', 6, 2)->nullable();
            $table->decimal('snf_7_6', 6, 2)->nullable();
            $table->decimal('snf_7_7', 6, 2)->nullable();
            $table->decimal('snf_7_8', 6, 2)->nullable();
            $table->decimal('snf_7_9', 6, 2)->nullable();
            $table->decimal('snf_8_0', 6, 2)->nullable();
            $table->decimal('snf_8_1', 6, 2)->nullable();
            $table->decimal('snf_8_2', 6, 2)->nullable();
            $table->decimal('snf_8_3', 6, 2)->nullable();
            $table->decimal('snf_8_4', 6, 2)->nullable();
            $table->decimal('snf_8_5', 6, 2)->nullable();
            $table->decimal('snf_8_6', 6, 2)->nullable();
            $table->decimal('snf_8_7', 6, 2)->nullable();
            $table->decimal('snf_8_8', 6, 2)->nullable();
            $table->decimal('snf_8_9', 6, 2)->nullable();
            $table->decimal('snf_9_0', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_rates');
    }
};
