<?php

// In DaftarHutang.php (Model)
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarHutang extends Model
{
    // Disable automatic timestamps
    public $timestamps = false;

    // Define the fillable columns (you should define this in your model)
    protected $fillable = [
        'user_id',
        'jumlah_transaksi',
        'deskripsi',
        'status_hutang',
        'tanggal_transaksi',
    ];
}
