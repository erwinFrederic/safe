<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        DB::table('positions')->insert([
            ['name' => 'Directeur général'],
            ['name' => 'Directeur adjoin'],
            ['name' => 'Directeur du service suivie'],
            ['name' => 'Directeur des opérations'],
            ['name' => 'Opérateur '],
            ['name' => 'Médecin'],
            ['name' => 'Secouriste'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
