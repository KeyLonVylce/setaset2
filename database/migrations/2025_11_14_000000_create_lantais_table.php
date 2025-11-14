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

        Schema::table('ruangans', function (Blueprint $table) {
            $table->foreignId('lantai_id')->nullable()->after('id')->constrained('lantais')->onDelete('cascade');
            $table->string('lantai', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropForeign(['lantai_id']);
            $table->dropColumn('lantai_id');
            $table->string('lantai', 20)->nullable(false)->change();
        });

        Schema::dropIfExists('lantais');
    }
};