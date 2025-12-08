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
        'transaction_status',
        'snap_token',
        'payment_type',
        'transaction_time',
        'fraud_status',
        'midtrans_response',
        'sewa_mulai',
        'sewa_berakhir',
        'is_renewal',
        'penalty_amount',
        'is_pengosongan',
        'pengosongan_dimulai',
        'pengosongan_berakhir',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'sewa_mulai' => 'datetime',
        'sewa_berakhir' => 'datetime',
        'pengosongan_dimulai' => 'datetime',
        'pengosongan_berakhir' => 'datetime',
        'is_renewal' => 'boolean',
        'is_pengosongan' => 'boolean',
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rak(): BelongsTo
    {
        return $this->belongsTo(Rak::class);
    }

    // Scopes
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

    // Attributes
    public function getStatusSewaAttribute()
    {
        if (!$this->sewa_berakhir || !in_array($this->transaction_status, ['capture', 'settlement'])) {
            return 'Tidak Aktif';
        }

        $now = Carbon::now()->startOfDay();
        $end = Carbon::parse($this->sewa_berakhir)->startOfDay();
        $daysDiff = $now->diffInDays($end, false);

        $gracePeriodDays = 3;
        $maxLateDays = 30;

        // Jika dalam masa pengosongan
        if ($this->is_pengosongan) {
            $pengosonganEnd = Carbon::parse($this->pengosongan_berakhir);
            $daysLeft = $now->diffInDays($pengosonganEnd, false);

            if ($daysLeft >= 0) {
                return "Pengosongan ({$daysLeft} hari tersisa)";
            } else {
                return "Selesai Pengosongan";
            }
        }

        // Logika status sewa normal
        if ($daysDiff > 0) {
            return "Aktif ({$daysDiff} hari tersisa)";
        } elseif ($daysDiff === 0) {
            return "Berakhir Hari Ini";
        } elseif (abs($daysDiff) <= $gracePeriodDays) {
            return "Masa Tenggang (Hari ke-" . abs($daysDiff) . ")";
        } else {
            $totalLateDays = abs($daysDiff) - $gracePeriodDays;
            if ($totalLateDays >= $maxLateDays) {
                return "Memasuki Pengosongan";
            }
            return "Terlambat ({$totalLateDays} hari)";
        }
    }

    public function getSisaHariAttribute()
    {
        if (!$this->sewa_berakhir || !in_array($this->transaction_status, ['capture', 'settlement'])) {
            return 0;
        }

        // Jika dalam masa pengosongan
        if ($this->is_pengosongan && $this->pengosongan_berakhir) {
            $now = Carbon::now()->startOfDay();
            $pengosonganEnd = Carbon::parse($this->pengosongan_berakhir)->startOfDay();
            return max(0, $now->diffInDays($pengosonganEnd, false));
        }

        // Sisa hari normal
        $now = Carbon::now()->startOfDay();
        $end = Carbon::parse($this->sewa_berakhir)->startOfDay();
        return max(0, $now->diffInDays($end, false));
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
