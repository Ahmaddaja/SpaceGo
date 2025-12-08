<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Auto-create tagihan ketika transaction dibuat
        try {
            // Cek apakah tagihan sudah ada (prevent duplicate)
            $existingTagihan = Tagihan::where('transaction_id', $transaction->id)->first();
            
            if (!$existingTagihan) {
                $parentTagihanId = null;
                
                // Jika renewal, cari parent tagihan
                if ($transaction->is_renewal && $transaction->parent_transaction_id) {
                    $parentTagihan = Tagihan::where('transaction_id', $transaction->parent_transaction_id)->first();
                    $parentTagihanId = $parentTagihan?->id;
                }
                
                Tagihan::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'rak_id' => $transaction->rak_id,
                    'harga_sewa' => $transaction->amount - ($transaction->penalty_amount ?? 0),
                    'penalty_amount' => $transaction->penalty_amount ?? 0,
                    'total_tagihan' => $transaction->amount,
                    'status' => $transaction->transaction_status ?? 'pending',
                    'type' => $transaction->is_renewal ? 'renewal' : 'sewa_baru',
                    'is_renewal' => $transaction->is_renewal ?? false,
                    'sewa_mulai' => $transaction->sewa_mulai,
                    'sewa_berakhir' => $transaction->sewa_berakhir,
                    'parent_tagihan_id' => $parentTagihanId,
                ]);

                Log::info('Tagihan auto-created', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->order_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-create tagihan: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id
            ]);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Sinkronisasi status transaction ke tagihan
        try {
            $tagihan = Tagihan::where('transaction_id', $transaction->id)->first();
            
            if ($tagihan) {
                $statusMap = [
                    'pending' => 'pending',
                    'settlement' => 'settlement',
                    'expired' => 'expired',
                    'failed' => 'failed',
                    'cancel' => 'cancel',
                ];

                $newStatus = $statusMap[$transaction->transaction_status] ?? $tagihan->status;
                
                $updateData = [
                    'status' => $newStatus,
                    'total_tagihan' => $transaction->amount,
                ];

                // Jika settlement, set paid_at
                if ($newStatus === 'settlement' && !$tagihan->paid_at) {
                    $updateData['paid_at'] = now();
                }

                // Jika expired atau cancel, set cancelled_at
                if (in_array($newStatus, ['expired', 'cancel']) && !$tagihan->cancelled_at) {
                    $updateData['cancelled_at'] = now();
                }

                $tagihan->update($updateData);

                Log::info('Tagihan status synced', [
                    'transaction_id' => $transaction->id,
                    'old_status' => $tagihan->status,
                    'new_status' => $newStatus
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync tagihan status: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id
            ]);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        // Tagihan akan auto-delete karena cascade di migration
        Log::info('Transaction deleted, tagihan will cascade delete', [
            'transaction_id' => $transaction->id
        ]);
    }
}