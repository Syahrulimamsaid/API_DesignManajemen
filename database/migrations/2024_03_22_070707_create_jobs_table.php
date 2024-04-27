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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id('id');
            $table->string('kode')->unique();
            $table->string('nama')->unique();
            $table->string('perusahaan');
            $table->date('tanggal_kirim')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('status_data')->default(false);
            $table->string('hasil_design')->nullable();
            $table->integer('status')->nullable();
            $table->integer('tanggapan_customer')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->timestamps();
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
