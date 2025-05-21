<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tbl_peminjaman', function (Blueprint $table) {
        $table->integer('jumlah')->default(1)->after('book_id');
    });
}

    /**
     * Reverse the migrations.
     */
 public function down()
{
    Schema::table('tbl_peminjaman', function (Blueprint $table) {
        $table->dropColumn('jumlah');
    });
}

    };
