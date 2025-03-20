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
            $table->string('name');
            $table->text('brief_history')->nullable();
            $table->string('vision');
            $table->jsonb('mission');
            $table->integer('village_funds'); // Dana desa
            $table->softDeletesTz();
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
