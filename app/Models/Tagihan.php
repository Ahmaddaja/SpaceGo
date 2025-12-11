<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Tagihan extends Model
{
    protected $table = 'tagihans';

    protected $fillable = [
        'tagihan_code',
        'transaction_id',
        'user_id',
        'rak_id',

        // Detail Tagihan
        'harga_sewa',
        'penalty_amount',
        'total_tagihan',

        // Status & Type
        'status',
        'type',
        'is_renewal',

        // Waktu
        'created_at_db',
        'expired_at',
        'paid_at',
        'cancelled_at',

        // Info Sewa
        'sewa_mulai',
        'sewa_berakhir',

        // Pengosongan Rak
        'is_pengosongan',
        'pengosongan_dimulai',
        'pengosongan_berakhir',
        'is_dikosongkan',
        'dikosongkan_at',

        // Parent tagihan (renewal)
        'parent_tagihan_id',
    ];

    protected $casts = [
        'harga_sewa' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_tagihan' => 'decimal:2',

        // boolean flags
        'is_renewal' => 'boolean',
        'is_pengosongan' => 'boolean',
        'is_dikosongkan' => 'boolean',

        // timestamps
        'created_at_db' => 'datetime',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',

        // sewa info (datetime in migration)
        'sewa_mulai' => 'datetime',
        'sewa_berakhir' => 'datetime',

        // pengosongan (new)
        'pengosongan_dimulai' => 'datetime',
        'pengosongan_berakhir' => 'datetime',
        'dikosongkan_at' => 'datetime',
    ];

    // Auto generate kode + expired_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tagihan) {
            if (empty($tagihan->tagihan_code)) {
                $tagihan->tagihan_code = 'BILL-' . strtoupper(uniqid());
            }

            // Set created_at_db otomatis
            if (empty($tagihan->created_at_db)) {
                $tagihan->created_at_db = now();
            }

            // Expired 24 jam hanya bila pending
            if (empty($tagihan->expired_at) && $tagihan->status === 'pending') {
                $tagihan->expired_at = Carbon::parse($tagihan->created_at_db)->addHours(24);
            }
        });
    }

    // Relations
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rak(): BelongsTo
    {
        return $this->belongsTo(Rak::class);
    }

    public function parentTagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'parent_tagihan_id');
    }

    public function renewalTagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, 'parent_tagihan_id');
    }

    // Scopes
    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeSettlement($q) { return $q->where('status', 'settlement'); }
    public function scopeExpired($q) { return $q->where('status', 'expired'); }
    public function scopeOverdue($q) { return $q->where('status', 'overdue'); }
    public function scopeForUser($q, $userId) { return $q->where('user_id', $userId); }

    // Helpers
    public function isExpired(): bool
    {
        return $this->status === 'pending'
            && $this->expired_at
            && now()->isAfter($this->expired_at);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'settlement',
            'paid_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);
    }

    public function getRemainingTimeAttribute(): ?string
    {
        if ($this->status !== 'pending' || !$this->expired_at) {
            return null;
        }

        $now = now();
        $expired = Carbon::parse($this->expired_at);

        if ($now->isAfter($expired)) {
            return 'Kadaluarsa';
        }

        return $now->diffForHumans($expired, true);
    }
}
