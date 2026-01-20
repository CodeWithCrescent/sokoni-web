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
        Schema::create('market_product_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('market_product_id')->constrained()->cascadeOnDelete();
            $table->integer('min_qty');
            $table->integer('max_qty')->nullable();
            $table->decimal('price', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['market_product_id', 'min_qty']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_prices');
    }
};
