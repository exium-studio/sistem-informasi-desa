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
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->jsonb('received_by');
            $table->foreignId('inbox_type_id')->constrained('inbox_types')->onDelete('cascade')->onUpdate('cascade');
            $table->text('message');
            $table->boolean('is_verified')->default(0); // yang butuh verifikasi
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboxes');
    }
};
