<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_rak',
        'gudang_id',
        'nama_rak',
        'jenis_rak',
        'deskripsi',
        'kapasitas_berat',
        'panjang',
        'lebar',
        'tinggi',
        'jumlah_tingkat',
        'lokasi_gudang',
        'harga_sewa_perbulan',
        'status',
        'foto',
        'spesifikasi_tambahan',
        'is_active',
        'durasi_sewa_hari'
    ];

    protected $casts = [
        'kapasitas_berat' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'jumlah_tingkat' => 'integer',
        'harga_sewa_perbulan' => 'integer',
        'is_active' => 'boolean',
    ];

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

    /**
     * Relasi ke FotoRak (Multiple Photos) - TAMBAHKAN INI
     */
    public function fotos()
    {
        return $this->hasMany(FotoRak::class)->orderBy('urutan');
    }

    /**
     * Relasi ke Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function firstPhoto()
    {
        return $this->hasOne(FotoRak::class)->orderBy('urutan');
    }

    // Ambil foto random atau foto pertama
    public function randomPhoto()
    {
        return $this->hasOne(FotoRak::class)->inRandomOrder();
    }

    public function getFotoUtamaAttribute()
    {
        // Ambil foto random dari relasi fotos (bukan primary lagi)
        $fotoRandom = $this->fotos()->inRandomOrder()->first();
        if ($fotoRandom) {
            return $fotoRandom->path;
        }

        // Jika tidak ada foto di fotos, ambil foto pertama
        $fotoFirst = $this->fotos()->first();
        if ($fotoFirst) {
            return $fotoFirst->path;
        }

        // Fallback ke kolom foto lama jika ada
        return $this->foto;
    }

    /**
     * Get URL foto utama
     */
    public function getFotoUtamaUrlAttribute()
    {
        $fotoUtama = $this->foto_utama;

        if ($fotoUtama) {
            return asset('storage/' . $fotoUtama);
        }

        return asset('images/no-image.png'); // placeholder jika tidak ada foto
    }

    /**
     * Check apakah rak memiliki foto
     */
    public function hasFotos()
    {
        return $this->fotos()->count() > 0 || !empty($this->foto);
    }

    /**
     * Get total jumlah foto
     */
    public function getTotalFotosAttribute()
    {
        $count = $this->fotos()->count();

        // Jika ada foto lama tapi tidak ada di fotos, tambah 1
        if ($count === 0 && !empty($this->foto)) {
            return 1;
        }

        return $count;
    }
}
