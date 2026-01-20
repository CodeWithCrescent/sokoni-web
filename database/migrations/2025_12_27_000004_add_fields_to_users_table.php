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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('profile_photo')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('profile_photo');
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'profile_photo', 'is_active', 'deleted_at']);
        });
    }
};
