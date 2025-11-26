<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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
        'midtrans_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'midtrans_response' => 'array'
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Rak
     */
    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    /**
     * Helper untuk format harga
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Scope untuk transaksi sukses
     */
    public function scopeSuccess($query)
    {
        return $query->whereIn('transaction_status', ['capture', 'settlement']);
    }

    /**
     * Scope untuk transaksi pending
     */
    public function scopePending($query)
    {
        return $query->where('transaction_status', 'pending');
    }

    /**
     * Scope untuk transaksi gagal
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('transaction_status', ['deny', 'expire', 'cancel']);
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccess()
    {
        return in_array($this->transaction_status, ['capture', 'settlement']);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending()
    {
        return $this->transaction_status === 'pending';
    }
}