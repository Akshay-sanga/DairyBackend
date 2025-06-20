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
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->nullable();
            $table->string('customer_account_number')->nullable();
            
            $table->string('date')->nullable();
            $table->string('category_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('product_price')->nullable();
            $table->string('qty')->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};
