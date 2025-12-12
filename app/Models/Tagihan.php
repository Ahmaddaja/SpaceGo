<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        'status_rak', // BARU
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

            // Set status_rak default
            if (empty($tagihan->status_rak)) {
                $tagihan->status_rak = 'tersedia';
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
            'status_rak' => 'terisi', // Update status rak
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);
    }

    /**
     * Update status rak berdasarkan kondisi sewa
     */
    public function updateStatusRak(): void
    {
        if (!$this->sewa_berakhir || $this->status !== 'settlement') {
            return;
        }

        $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
        $now = Carbon::parse($currentDbTime);
        $end = Carbon::parse($this->sewa_berakhir);

        // Hitung selisih dalam menit
        $totalMinutes = $now->diffInMinutes($end, false);

        $gracePeriodDays = 3;
        $maxLateDays = 30;
        $pengosonganDays = 7;

        $gracePeriodMinutes = $gracePeriodDays * 24 * 60;
        $maxLateMinutes = $maxLateDays * 24 * 60;
        $pengosonganMinutes = $pengosonganDays * 24 * 60;

        $newStatusRak = 'terisi';
        $updateData = ['status_rak' => 'terisi'];

        if ($totalMinutes >= 0) {
            // MASIH DALAM MASA SEWA AKTIF
            $newStatusRak = 'terisi';
        } elseif (abs($totalMinutes) <= $gracePeriodMinutes) {
            // MASA TENGGANG (0-3 hari setelah berakhir)
            $newStatusRak = 'masa_tenggang';
        } elseif (abs($totalMinutes) <= ($gracePeriodMinutes + $maxLateMinutes)) {
            // TERLAMBAT (3-33 hari setelah berakhir, kena denda)
            $newStatusRak = 'terlambat';
        } elseif (abs($totalMinutes) <= ($gracePeriodMinutes + $maxLateMinutes + $pengosonganMinutes)) {
            // MASA PENGOSONGAN (33-40 hari setelah berakhir)
            $newStatusRak = 'pengosongan';
            
            if (!$this->is_pengosongan) {
                $updateData['is_pengosongan'] = true;
                $updateData['pengosongan_dimulai'] = $now;
                $updateData['pengosongan_berakhir'] = $now->copy()->addDays($pengosonganDays);
            }
        } else {
            // DIKOSONGKAN (lebih dari 40 hari)
            $newStatusRak = 'dikosongkan';
            
            if (!$this->is_dikosongkan) {
                $updateData['is_dikosongkan'] = true;
                $updateData['dikosongkan_at'] = $now;
            }
        }

        $updateData['status_rak'] = $newStatusRak;
        $this->update($updateData);

        // Update status rak di tabel raks
        if ($this->rak) {
            $rakStatus = 'tersedia';
            
            if ($newStatusRak === 'dikosongkan') {
                $rakStatus = 'tersedia';
            } elseif ($newStatusRak === 'pengosongan') {
                $rakStatus = 'pengosongan';
            } elseif (in_array($newStatusRak, ['terisi', 'masa_tenggang', 'terlambat'])) {
                $rakStatus = 'terisi';
            }
            
            $this->rak->update(['status' => $rakStatus]);
        }
    }

    /**
     * Get status rak dengan informasi tambahan
     */
    public function getStatusRakInfo(): array
    {
        $this->updateStatusRak(); // Update dulu
        
        $statusInfo = [
            'status' => $this->status_rak,
            'label' => '',
            'color' => '',
            'icon' => '',
            'description' => '',
        ];

        switch ($this->status_rak) {
            case 'terisi':
                $statusInfo['label'] = 'Terisi';
                $statusInfo['color'] = 'green';
                $statusInfo['icon'] = 'fa-check-circle';
                $statusInfo['description'] = 'Rak sedang disewa dan aktif';
                break;
            
            case 'masa_tenggang':
                $statusInfo['label'] = 'Masa Tenggang';
                $statusInfo['color'] = 'yellow';
                $statusInfo['icon'] = 'fa-clock';
                $statusInfo['description'] = 'Masa sewa telah berakhir, dalam masa tenggang 3 hari';
                break;
            
            case 'terlambat':
                $statusInfo['label'] = 'Terlambat';
                $statusInfo['color'] = 'orange';
                $statusInfo['icon'] = 'fa-exclamation-triangle';
                $statusInfo['description'] = 'Terlambat pembayaran, dikenakan denda';
                break;
            
            case 'pengosongan':
                $statusInfo['label'] = 'Pengosongan';
                $statusInfo['color'] = 'purple';
                $statusInfo['icon'] = 'fa-box-open';
                $statusInfo['description'] = 'Masa pengosongan 7 hari';
                break;
            
            case 'dikosongkan':
                $statusInfo['label'] = 'Dikosongkan';
                $statusInfo['color'] = 'blue';
                $statusInfo['icon'] = 'fa-archive';
                $statusInfo['description'] = 'Rak telah dikosongkan dan tersedia kembali';
                break;
            
            default:
                $statusInfo['label'] = 'Tersedia';
                $statusInfo['color'] = 'gray';
                $statusInfo['icon'] = 'fa-cube';
                $statusInfo['description'] = 'Rak tersedia untuk disewa';
        }

        return $statusInfo;
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