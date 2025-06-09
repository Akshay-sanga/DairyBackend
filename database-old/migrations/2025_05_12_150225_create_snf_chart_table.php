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
        Schema::create('snf_chart', function (Blueprint $table) {
            $table->id();
            $table->decimal('fat', 4, 1)->nullable();
            $table->decimal('clr_22', 4, 1)->nullable();
            $table->decimal('clr_23', 4, 1)->nullable();
            $table->decimal('clr_24', 4, 1)->nullable();
            $table->decimal('clr_25', 4, 1)->nullable();
            $table->decimal('clr_26', 4, 1)->nullable();
            $table->decimal('clr_27', 4, 1)->nullable();
            $table->decimal('clr_28', 4, 1)->nullable();
            $table->decimal('clr_29', 4, 1)->nullable();
            $table->decimal('clr_30', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snf_chart');
    }
};
