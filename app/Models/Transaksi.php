<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'user_id',
        'kode_transaksi',
        'tanggal',  
        'total',
        'tunai',
        'kembalian',
        'status',
        'subtotal',
    ];
}
