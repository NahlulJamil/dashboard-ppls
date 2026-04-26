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
        Schema::create('data_plps', function (Blueprint $table) {
            $table->id();

            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();

            $table->foreignId('kegiatan_id')->constrained('kegiatans');
            $table->foreignId('instansi_id')->constrained('instansis');

            $table->integer('sks');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->year('tahun');
            $table->string('tahun_ajaran');

            $table->string('penyelenggara')->nullable();
            $table->string('program_owner')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_plps');
    }
};
