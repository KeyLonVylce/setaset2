<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lantais', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lantai', 50)->unique();
            $table->integer('urutan')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tambah FK di ruangans setelah tabel lantais & pejabats sudah ada
        Schema::table('ruangans', function (Blueprint $table) {
            $table->foreign('lantai_id')
                ->references('id')
                ->on('lantais')
                ->onDelete('set null');

            $table->foreign('penanggung_jawab_id')
                ->references('id')
                ->on('pejabats')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropForeign(['lantai_id']);
            $table->dropForeign(['penanggung_jawab_id']);
        });

        Schema::dropIfExists('lantais');
    }
};