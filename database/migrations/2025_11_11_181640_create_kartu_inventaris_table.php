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
    Schema::create('kartu_inventaris', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
        $table->date('tanggal')->nullable();
        $table->foreignId('mengetahui_id')->nullable()->constrained('pejabats');
        $table->foreignId('penanggung_jawab_id')->nullable()->constrained('pejabats');
        $table->foreignId('pengelola_id')->nullable()->constrained('pejabats');
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_inventaris');
    }
};
