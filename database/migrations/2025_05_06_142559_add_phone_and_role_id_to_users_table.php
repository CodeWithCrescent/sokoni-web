<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email_verified_at');
            $table->string('profile_photo_path')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->foreignId('role_id')->after('remember_token')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('profile_photo_path');
            $table->dropColumn('last_active_at');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
