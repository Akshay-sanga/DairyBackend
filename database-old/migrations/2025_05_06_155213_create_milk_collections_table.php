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
        Schema::create('milk_collections', function (Blueprint $table) {
            $table->id();
            $table->string('customer_account_number')->nullable();
            $table->string('milk_type')->nullable();
            $table->string('quantity')->nullable();
            $table->string('clr')->nullable();
            $table->string('fat')->nullable();
            $table->string('snf')->nullable();
            $table->string('base_rate')->nullable();
            $table->string('other_price')->nullable();
            $table->string('name')->nullable();
            $table->string('spouse')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_collections');
    }
};
