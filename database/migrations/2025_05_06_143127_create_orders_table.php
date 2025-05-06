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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->dateTime('order_date');
            $table->string('status', 50);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            
            $table->foreign('delivery_id')->references('id')->on('delivery_personnel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
