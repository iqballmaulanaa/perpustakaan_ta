<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_peminjaman';

    protected $fillable = [
        'resi_pjmn',
        'member_id',
        'book_id',
         'jumlah',
        'created_at',
        'updated_at',
        'deleted_at',
        'return_date'
    ];

    protected $dates = [
        'return_date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class, 'resi_pjmn', 'resi_pjmn');
    }

    // Relasi ke banyak buku (pivot table)
    // Peminjaman.php
public function books()
{
    return $this->belongsToMany(Book::class, 'peminjaman_buku', 'peminjaman_id', 'buku_id');
}
}
