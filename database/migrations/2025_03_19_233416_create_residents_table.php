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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('religion_id')->constrained('religions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('education_id')->constrained('educations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('job_type_id')->constrained('job_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('blood_type_id')->constrained('blood_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('maried_status_id')->constrained('maried_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('relationship_status_id')->constrained('relationship_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('citizenship_id')->constrained('citizenships')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('gender'); // 1 = male, 0 = female
            $table->string('place_of_birth');
            $table->string('date_of_birth');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
