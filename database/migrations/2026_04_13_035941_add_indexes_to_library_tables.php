<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('status');
            $table->index('jurusan');
            $table->index('kelas');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->index('judul');
            $table->index('penulis');
            $table->index('lokasi_rak');
            $table->index('stok');
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->index('status');
            $table->index('tanggal_pinjam');
            $table->index('tanggal_jatuh_tempo');
            $table->index('tanggal_kembali');
            $table->index('user_id');
            $table->index('book_id');
            $table->index('admin_id');
        });

        Schema::table('return_books', function (Blueprint $table) {
            $table->index('tanggal_dikembalikan');
            $table->index('kondisi_buku');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['jurusan']);
            $table->dropIndex(['kelas']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['judul']);
            $table->dropIndex(['penulis']);
            $table->dropIndex(['lokasi_rak']);
            $table->dropIndex(['stok']);
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_pinjam']);
            $table->dropIndex(['tanggal_jatuh_tempo']);
            $table->dropIndex(['tanggal_kembali']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['book_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('return_books', function (Blueprint $table) {
            $table->dropIndex(['tanggal_dikembalikan']);
            $table->dropIndex(['kondisi_buku']);
            $table->dropIndex(['admin_id']);
        });
    }
};