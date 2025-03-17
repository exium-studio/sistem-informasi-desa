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
        Schema::create('family_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade'); // Kepala Keluarga
            $table->string('no_kk', 16)->nullable()->unique();
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->jsonb('village'); // Kelurahan
            $table->jsonb('sub_district'); // Kecamatan
            $table->jsonb('city_regency'); // Kota/Kabupaten
            $table->jsonb('province'); // Provinsi
            $table->string('postal_code', 5); // Kode Pos
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_cards');
    }
};
