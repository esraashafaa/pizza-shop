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
        Schema::create('order_pizza_topping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_pizza_id')->constrained('order_pizza')->onDelete('cascade');
            $table->foreignId('topping_id')->constrained('toppings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_pizza_topping');
    }
};
