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

            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('sub_program_id')->constrained('sub_programs');

            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();

            $table->foreignId('kegiatan_id')->constrained('kegiatans');
            $table->foreignId('mitra_id')->constrained('mitras');

            $table->integer('sks');
            $table->enum('semester', ['GANJIL', 'GENAP']);
            $table->string('tahun_ajaran');
            $table->string('semester_ta');
            $table->enum('penyelenggara', ['Eksternal', 'Internal']);
            $table->string('dosen_pembimbing')->nullable();

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
