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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_file')->nullable()->constrained('documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('image_file')->nullable()->constrained('documents')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->text('summary')->nullable();
            $table->text('gmaps');
            $table->string('vision');
            $table->jsonb('mission');
            $table->integer('village_funds')->default(0); // Dana desa
            $table->integer('village_area')->default(0); // Luas desa dalam meter persegi
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
