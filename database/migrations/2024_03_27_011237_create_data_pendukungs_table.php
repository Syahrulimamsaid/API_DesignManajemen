<?php

use App\Models\Job;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_pendukungs', function (Blueprint $table) {
            $table->id('id');
            $table->string('job_kode');
            $table->foreign('job_kode')->references('kode')->on('jobs')->onDelete('cascade');
            $table->string('nama')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pendukungs');
    }
};
