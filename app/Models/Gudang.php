<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'deskripsi',
        'foto',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function raks()
    {
        return $this->hasMany(Rak::class, 'lokasi_gudang', 'nama_gudang');
    }

    public function getJumlahRakAttribute()
    {
        return $this->raks()->count();
    }

    public function getJumlahRakTersediaAttribute()
    {
        return $this->raks()->where('status', 'tersedia')->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}