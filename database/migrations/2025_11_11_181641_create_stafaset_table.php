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
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['staff', 'admin'])->default('staff');
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expired_at')->nullable();
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
