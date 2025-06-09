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
        Schema::create('daily_milk_sales', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->nullable();
            $table->string('sale_date')->nullable();
            $table->string('shift')->nullable();
            $table->string('milk_type')->nullable();
            $table->string('quentity')->nullable();
            $table->string('rate_per_liter')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_milk_sales');
    }
};
