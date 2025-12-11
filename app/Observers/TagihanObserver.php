<?php

namespace App\Observers;

use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;

class TagihanObserver
{
    /**
     * Handle the Tagihan "created" event.
     */
    public function created(Tagihan $tagihan): void
    {
        // Set status_rak default saat dibuat
        if (empty($tagihan->status_rak)) {
            $tagihan->status_rak = 'tersedia';
            $tagihan->saveQuietly();
        }
    }

    /**
     * Handle the Tagihan "updated" event.
     */
    public function updated(Tagihan $tagihan): void
    {
        // Jika status berubah menjadi settlement, set status_rak ke terisi
        if ($tagihan->isDirty('status') && $tagihan->status === 'settlement') {
            if ($tagihan->status_rak !== 'terisi') {
                $tagihan->status_rak = 'terisi';
                $tagihan->saveQuietly();
            }
        }

        // Update status rak di tabel raks sesuai status_rak
        if ($tagihan->isDirty('status_rak') && $tagihan->rak) {
            $this->syncRakStatus($tagihan);
        }
    }

    /**
     * Sync status rak berdasarkan status_rak tagihan
     */
    private function syncRakStatus(Tagihan $tagihan): void
    {
        if (!$tagihan->rak) {
            return;
        }

        $rakStatus = 'tersedia';

        switch ($tagihan->status_rak) {
            case 'terisi':
            case 'masa_tenggang':
            case 'terlambat':
                $rakStatus = 'terisi';
                break;
            
            case 'pengosongan':
                $rakStatus = 'pengosongan';
                break;
            
            case 'dikosongkan':
            case 'tersedia':
                $rakStatus = 'tersedia';
                break;
        }

        if ($tagihan->rak->status !== $rakStatus) {
            $tagihan->rak->update(['status' => $rakStatus]);
            
            Log::info('Rak status synced from tagihan', [
                'tagihan_id' => $tagihan->id,
                'rak_id' => $tagihan->rak_id,
                'tagihan_status_rak' => $tagihan->status_rak,
                'rak_status' => $rakStatus
            ]);
        }
    }

    /**
     * Handle the Tagihan "deleted" event.
     */
    public function deleted(Tagihan $tagihan): void
    {
        //
    }

    /**
     * Handle the Tagihan "restored" event.
     */
    public function restored(Tagihan $tagihan): void
    {
        //
    }

    /**
     * Handle the Tagihan "force deleted" event.
     */
    public function forceDeleted(Tagihan $tagihan): void
    {
        //
    }
}