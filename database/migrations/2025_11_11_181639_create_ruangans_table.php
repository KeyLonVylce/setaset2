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
        $table->string('lantai', 20)->nullable();
        $table->string('penanggung_jawab', 100)->nullable();
        $table->string('nip_penanggung_jawab', 30)->nullable();
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
