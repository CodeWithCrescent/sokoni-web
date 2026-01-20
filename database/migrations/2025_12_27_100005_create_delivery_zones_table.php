<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_fee', 10, 2)->default(0);
            $table->decimal('per_km_fee', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('estimated_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('delivery_zone_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('delivery_zone_id')->constrained()->cascadeOnDelete();
            $table->string('area_name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('radius_km', 8, 2)->default(5);
            $table->timestamps();

            $table->index('delivery_zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zone_areas');
        Schema::dropIfExists('delivery_zones');
    }
};
