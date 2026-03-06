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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan', 100);

            // Kolom FK tanpa constraint dulu —
            // constraint ditambah setelah tabel lantais & stafaset dibuat
            $table->unsignedBigInteger('lantai_id')->nullable();
            $table->unsignedBigInteger('penanggung_jawab_id')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};