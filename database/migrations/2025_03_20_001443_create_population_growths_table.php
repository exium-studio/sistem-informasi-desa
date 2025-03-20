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
        Schema::create('population_growths', function (Blueprint $table) {
            $table->id();
            $table->integer('citizen_total');
            $table->integer('new_citizen_total');
            $table->integer('leave_citizen_total');
            $table->integer('year');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_growths');
    }
};
