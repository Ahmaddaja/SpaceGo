<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $raks = Rak::all();
        $customers = User::where('role', 'customer')->get();

        $transactionStatuses = ['capture', 'settlement', 'pending', 'deny', 'expire', 'cancel'];
        $paymentTypes = ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'ovo', 'dana'];

        echo "🗓️  Generating dummy data for July–December 2025\n";

        $startMonth = 7;
        $endMonth = 12;
        $year = 2025;

        for ($month = $startMonth; $month <= $endMonth; $month++) {

            $transactionCountForMonth = rand(8, 20);

            // 70% probabilitas sukses
            $statusWeights = [35, 35, 15, 5, 5, 5];

            for ($i = 0; $i < $transactionCountForMonth; $i++) {

                $customer = $customers->random();
                $rak = $raks->random();

                // Weighted status
                $status = $this->weightedRandom($transactionStatuses, $statusWeights);

                // Random tanggal transaksi
                $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
                $day = rand(1, $daysInMonth);

                $transactionDate = Carbon::create($year, $month, $day, rand(8, 22), rand(0, 59), rand(0, 59));

                $amount = $rak->harga_sewa_perbulan;

                // Unique Order ID
                $orderId = 'ORDER-' . $transactionDate->format('YmdHis') . '-' . $rak->id . '-' . $customer->id;

                Transaction::create([
                    'order_id'           => $orderId,
                    'user_id'            => $customer->id,
                    'rak_id'             => $rak->id,
                    'amount'             => $amount,
                    'transaction_status' => $status,
                    'payment_type'       => collect($paymentTypes)->random(),
                    'snap_token'         => 'snap_' . $orderId,
                    'fraud_status'       => rand(1, 10) <= 9 ? 'accept' : 'challenge',
                    'transaction_time'   => $transactionDate,
                    'midtrans_response'  => null,
                ]);
            }
        }

        echo "✅ Transaction seeding completed.\n";
    }

    private function weightedRandom(array $items, array $weights)
    {
        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);

        foreach ($items as $index => $item) {
            $rand -= $weights[$index];
            if ($rand <= 0) {
                return $item;
            }
        }

        return $items[0];
    }
}
