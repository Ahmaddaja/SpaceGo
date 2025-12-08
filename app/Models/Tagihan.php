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
        'harga_sewa',
        'penalty_amount',
        'total_tagihan',
        'status',
        'type',
        'is_renewal',
        'created_at_db',
        'expired_at',
        'paid_at',
        'cancelled_at',
        'sewa_mulai',
        'sewa_berakhir',
        'parent_tagihan_id',
    ];

    protected $casts = [
        'harga_sewa' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'is_renewal' => 'boolean',
        'created_at_db' => 'datetime',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'sewa_mulai' => 'date',
        'sewa_berakhir' => 'date',
    ];

    // Boot method untuk auto-generate tagihan_code dan expired_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tagihan) {
            if (empty($tagihan->tagihan_code)) {
                $tagihan->tagihan_code = 'BILL-' . strtoupper(uniqid());
            }
            
            // Set created_at_db dari database timestamp
            if (empty($tagihan->created_at_db)) {
                $tagihan->created_at_db = now();
            }
            
            // Set expired_at 24 jam dari created_at_db
            if (empty($tagihan->expired_at) && $tagihan->status === 'pending') {
                $tagihan->expired_at = Carbon::parse($tagihan->created_at_db)->addHours(24);
            }
        });
    }

    // Relations
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
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
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSettlement($query)
    {
        return $query->where('status', 'settlement');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    public function isExpired(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->expired_at && now()->isAfter($this->expired_at);
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

    public function markAsOverdue(): void
    {
        $this->update([
            'status' => 'overdue',
        ]);
    }

    public function getRemainingTimeAttribute(): ?string
    {
        if ($this->status !== 'pending' || !$this->expired_at) {
            return null;
        }

        $now = now();
        $expiredAt = Carbon::parse($this->expired_at);

        if ($now->isAfter($expiredAt)) {
            return 'Kadaluarsa';
        }

        return $now->diffForHumans($expiredAt, true);
    }
}