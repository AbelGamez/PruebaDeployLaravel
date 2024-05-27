<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->boolean("stattrak");
            $table->double("float");
            $table->integer("units");
            $table->double("unit_price");

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->integer("available");

            $table->unsignedBigInteger('payment_id')->nullable(); 
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
          
            $table->timestamps();     
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('stocks');
    }
};