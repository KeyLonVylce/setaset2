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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ruangan_id')
                ->constrained('ruangans')
                ->onDelete('cascade');

            $table->integer('no_urut')->nullable();
            $table->string('nama_barang', 150)->nullable();
            $table->string('merk_model', 150)->nullable();
            $table->string('no_seri_pabrik', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
            $table->string('bahan', 100)->nullable();
            $table->year('tahun_pembuatan')->nullable();
            $table->string('kode_barang', 50)->nullable();
            $table->integer('jumlah')->default(0);

            // FIX: gunakan decimal untuk nilai uang
            $table->decimal('harga_perolehan', 15, 2)->nullable();

            $table->enum('kondisi', ['B', 'KB', 'RB'])->default('B');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
