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
        Schema::create('app_detail_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('nama');
            $table->string('email');
            $table->string('no_kk');
            $table->string('telepon');
            $table->string('foto')->nullable();
            $table->timestamps();
            
            $table->foreign('username')->references('username')->on('apps_login')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_detail_orang_tua');
    }
};
