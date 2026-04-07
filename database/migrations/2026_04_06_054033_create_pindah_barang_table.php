<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pindah_barangs', function (Blueprint $table) {
            $table->id();

            // relasi ke barang
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');

            // ruangan asal & tujuan
            $table->foreignId('ruangan_asal')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('ruangan_tujuan')->constrained('ruangans')->onDelete('cascade');

            // jumlah barang dipindahkan
            $table->integer('jumlah_pindah');

            // catatan
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pindahbarangs');
    }
};