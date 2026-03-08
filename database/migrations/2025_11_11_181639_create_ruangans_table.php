<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan', 100);

            // FK ke lantais — ditambah constraint setelah tabel lantais dibuat
            // (lihat migration create_lantais_table)
            $table->unsignedBigInteger('lantai_id')->nullable();

            // FK ke pejabats — menggantikan kolom string penanggung_jawab
            // dan nip_penanggung_jawab yang redundan
            $table->unsignedBigInteger('penanggung_jawab_id')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};