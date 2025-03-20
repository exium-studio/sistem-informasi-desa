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
        Schema::create('official_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_person')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('type', ['whatsapp', 'telephone', 'instagram', 'facebook', 'x', 'email', 'website']);
            $table->string('value');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_contacts');
    }
};
