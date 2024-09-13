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
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete()->after('id');
            $table->foreignId('emergency_id')->nullable()->constrained('emergencies')->cascadeOnDelete()->after('role_id');
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnDelete()->after('emergency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropForeign(['emergency_id']);
            $table->dropColumn('emergency_id');
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
