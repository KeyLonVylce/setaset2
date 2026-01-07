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
    Schema::create('stafaset', function (Blueprint $table) {
        $table->id();
        $table->string('username', 50)->unique();
        $table->string('nama', 150);
        $table->string('nip', 30)->unique();
        $table->string('password');
        $table->enum('role', ['staff', 'admin'])->default('staff'); // ✅ TAMBAHKAN INI
        $table->boolean('can_edit')->default(true);
        $table->rememberToken();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stafaset');
    }
};
