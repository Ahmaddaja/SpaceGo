<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FotoRak extends Model
{
    use HasFactory;

    protected $table = 'foto_rak';

    protected $fillable = [
        'rak_id',
        'path',
        // 'is_primary',
        'urutan'
    ];

    // protected $casts = [
    //     'is_primary' => 'boolean',
    // ];

    /**
     * Relasi ke Rak
     */
    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    /**
     * Get full URL foto
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Delete foto dari storage
     */
    public function deleteFile()
    {
        if (Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }
    }
}