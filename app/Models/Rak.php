<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_rak',
        'nama_rak',
        'jenis_rak',
        'deskripsi',
        'kapasitas_berat',
        'panjang',
        'lebar',
        'tinggi',
        'jumlah_tingkat',
        'lokasi_gudang', //'zona_gudang',
        'harga_sewa_perbulan',
        'status',
        'foto',
        'spesifikasi_tambahan',
        'is_active'
    ];

    protected $casts = [
        'kapasitas_berat' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'jumlah_tingkat' => 'integer',
        'harga_sewa_perbulan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Tambahkan ini untuk set default value
    protected $attributes = [
        'status' => 'tersedia',
        'is_active' => true,
    ];

    public function getHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_sewa_perbulan, 0, ',', '.');
    }

    public function getVolumeAttribute()
    {
        return $this->panjang * $this->lebar * $this->tinggi;
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'lokasi_gudang', 'nama_gudang');
    }
    
}
