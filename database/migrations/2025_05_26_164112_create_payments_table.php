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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->nullable();
            $table->string('head_dairy_id')->nullable();
            $table->string('customer_account_number')->nullable();
            $table->string('date')->nullable();
            $table->string('amount')->nullable();
            $table->string('type')->nullable();
            $table->string('mode')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
