<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peminjaman_buku', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peminjaman_id');
            $table->unsignedBigInteger('buku_id');
            $table->timestamps();

            // Foreign key constraints (jika tabel terkait sudah ada)
            $table->foreign('peminjaman_id')->references('id')->on('tbl_peminjaman')->onDelete('cascade');
            $table->foreign('buku_id')->references('id')->on('tbl_books')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_buku');
    }
};
