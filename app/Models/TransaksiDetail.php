<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $fillable = [
        'transaksi_id',
        'rak_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];
}
