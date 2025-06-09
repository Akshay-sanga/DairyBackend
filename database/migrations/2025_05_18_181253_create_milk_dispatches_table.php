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
        Schema::create('milk_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->nullable();
            $table->string('dispatch_date')->nullable();
            $table->string('shift')->nullable();
            $table->string('head_dairy_id')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('milk_details')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_dispatches');
    }
};
