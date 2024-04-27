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
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id('id');
            $table->string('kode')->unique();
            $table->string('job_kode');
            $table->foreign('job_kode')->references('kode')->on('jobs')->onDelete('cascade');
            $table->string('designer_kode');
            $table->foreign('designer_kode')->references('kode')->on('users')->onDelete('cascade');
            $table->date('tanggal_pengumpulan');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};
