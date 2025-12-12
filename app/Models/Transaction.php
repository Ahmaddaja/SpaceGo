<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'rak_id',
        'amount',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'transaction_time',
        'snap_token',
        'midtrans_response',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'amount' => 'decimal:2',
        'midtrans_response' => 'array',
    ];

    // ====================
    // Relationships
    // ====================
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rak(): BelongsTo
    {
        return $this->belongsTo(Rak::class);
    }

    // Satu transaksi memiliki satu tagihan
    // (karena transaction_id ada di tabel tagihans)
    public function tagihan()
    {
        return $this->hasOne(Tagihan::class, 'transaction_id');
    }

    // ====================
    // Scopes
    // ====================
    public function scopeSuccess($query)
    {
        return $query->whereIn('transaction_status', ['capture', 'settlement']);
    }

    public function scopePending($query)
    {
        return $query->where('transaction_status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('transaction_status', ['deny', 'expire', 'cancel']);
    }

    // ====================
    // UI Helpers
    // ====================
    public function getStatusBadgeColor()
    {
        return match ($this->transaction_status) {
            'capture', 'settlement' => 'success',
            'pending' => 'warning',
            'deny', 'expire', 'cancel' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusIcon()
    {
        return match ($this->transaction_status) {
            'capture', 'settlement' => 'fas fa-check-circle',
            'pending' => 'fas fa-clock',
            'deny', 'expire', 'cancel' => 'fas fa-times-circle',
            default => 'fas fa-question-circle',
        };
    }
}
