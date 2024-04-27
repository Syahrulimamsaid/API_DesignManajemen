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
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id('id');
            $table->string('kode')->unique();
            $table->string('job_assignment_kode');
            $table->foreign('job_assignment_kode')->references('kode')->on('job_assignments')->onDelete('cascade');
            $table->string('petugas_kode');
            $table->foreign('petugas_kode')->references('kode')->on('users')->onUpdate('cascade');
            $table->string('komentar')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};
