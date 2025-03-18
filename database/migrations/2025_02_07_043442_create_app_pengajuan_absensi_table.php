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
        Schema::create('apps_pengajuan_absensi', function (Blueprint $table) {
            $table->id();
            $table->integer('id_siswa');
            $table->unsignedBigInteger('id_orang_tua');
            $table->string('jenis_izin', 50);
            $table->text('keterangan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('file_path')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('apps_pengajuan_absensi', function (Blueprint $table) {
            $table->foreign('id_siswa')
                  ->references('id')
                  ->on('mstr_siswa')
                  ->cascadeOnDelete();
                  
            $table->foreign('id_orang_tua')
                  ->references('id')
                  ->on('apps_detail_orang_tua')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps_pengajuan_absensi');
    }
};
