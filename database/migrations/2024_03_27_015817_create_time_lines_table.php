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
        Schema::create('time_lines', function (Blueprint $table) {
            $table->id('id');
            $table->string('event');
            $table->timestamp('tanggal_event');
            $table->string('job_assignment_kode');
            $table->foreign('job_assignment_kode')->references('kode')->on('job_assignments')->onDelete('cascade');
            $table->string('quality_control_kode')->nullable();
             $table->foreign('quality_control_kode')->references('kode')->on('quality_controls');
             $table->timestamp('mulai_pengerjaan')->nullable();
             $table->timestamp('selesai_pengerjaan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_lines');
    }
};
