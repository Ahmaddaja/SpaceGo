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

    // app/Models/Transaction.php
public function getStatusBadgeColor()
{
    switch ($this->transaction_status) {
        case 'settlement':
        case 'success':
            return 'success';
        case 'pending':
            return 'warning';
        case 'deny':
        case 'expire':
        case 'cancel':
            return 'danger';
        case 'capture':
            return 'info';
        default:
            return 'secondary';
    }
}

// Method untuk mendapatkan icon berdasarkan status
public function getStatusIcon()
{
    switch ($this->transaction_status) {
        case 'settlement':
        case 'success':
            return 'fas fa-check-circle';
        case 'pending':
            return 'fas fa-clock';
        case 'deny':
        case 'expire':
        case 'cancel':
            return 'fas fa-times-circle';
        case 'capture':
            return 'fas fa-camera';
        default:
            return 'fas fa-question-circle';
    }
}

public function getSisaHariAttribute()
{
    if (!$this->sewa_berakhir) {
        return null;
    }

    return now()->diffInDays($this->sewa_berakhir, false);
}

public function getStatusSewaAttribute()
{
    $now = now()->startOfDay();
    $end = \Carbon\Carbon::parse($this->sewa_berakhir)->startOfDay();

    if ($now->gt($end)) {
        return 'masa_tenggang';
    } elseif ($now->eq($end)) {
        return 'berakhir_hari_ini';
    } else {
        return 'aktif';
    }
}

public function parent()
{
    return $this->belongsTo(Transaction::class, 'parent_transaction_id');
}

public function renewals()
{
    return $this->hasMany(Transaction::class, 'parent_transaction_id')
        ->where('is_renewal', true);
}

// Tambahkan method ini untuk menangani pembayaran berhasil renewal
public function handleRenewalSuccess()
{
    \DB::beginTransaction();
    
    try {
        $originalTransaction = $this->parent;
        $rak = $this->rak;
        
        if ($originalTransaction && $rak) {
            // Calculate new sewa_berakhir date
            $durasi = $rak->durasi_sewa_hari ?? 30;
            
            // Start from the original sewa_berakhir or today
            $startDate = $originalTransaction->sewa_berakhir > now() 
                ? $originalTransaction->sewa_berakhir 
                : now();
            
            $this->sewa_mulai = $startDate;
            $this->sewa_berakhir = $startDate->copy()->addDays($durasi);
            $this->save();
            
            // Update original transaction
            if ($originalTransaction->sewa_berakhir < now()) {
                $originalTransaction->sewa_berakhir = $this->sewa_berakhir;
                $originalTransaction->save();
            }
            
            // Update rak status
            if ($rak->status !== 'terisi') {
                $rak->update(['status' => 'terisi']);
            }
            
            \DB::commit();
            return true;
        }
        
        \DB::rollBack();
        return false;
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Handle Renewal Error: ' . $e->getMessage());
        return false;
    }
}
}