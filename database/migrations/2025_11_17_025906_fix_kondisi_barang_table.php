<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('barangs', 'keadaan_baik')) {
                $table->dropColumn(['keadaan_baik', 'keadaan_kurang_baik', 'keadaan_rusak_berat']);
            }
            
            // Make sure kondisi column exists
            if (!Schema::hasColumn('barangs', 'kondisi')) {
                $table->enum('kondisi', ['B', 'KB', 'RB'])->default('B')->after('harga_perolehan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'keadaan_baik')) {
                $table->integer('keadaan_baik')->default(0);
                $table->integer('keadaan_kurang_baik')->default(0);
                $table->integer('keadaan_rusak_berat')->default(0);
            }
            
            if (Schema::hasColumn('barangs', 'kondisi')) {
                $table->dropColumn('kondisi');
            }
        });
    }
};