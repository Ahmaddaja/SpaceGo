<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raks = Rak::all();
        $customers = User::where('role', 'customer')->get();

        if ($raks->isEmpty()) {
            $this->command->error('No raks found. Please run DatabaseSeeder first.');
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->error('No customers found. Please run DatabaseSeeder first.');
            return;
        }

        $transactionStatuses = ['capture', 'settlement', 'pending', 'deny', 'expire', 'cancel'];
        $paymentTypes = ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'ovo', 'dana'];

        $this->command->info('Generating transaction dummy data...');

        // Generate transactions for the last 6 months
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $totalTransactions = 0;

        // Loop through each month
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $transactionsPerMonth = rand(8, 15); // 8-15 transactions per month

            for ($i = 0; $i < $transactionsPerMonth; $i++) {
                $customer = $customers->random();
                $rak = $raks->random();

                // Bias towards successful transactions (70% success rate)
                $statusWeights = [
                    25, // capture
                    25, // settlement
                    20, // pending
                    10, // deny
                    10, // expire
                    10  // cancel
                ];

                $status = $this->weightedRandom($transactionStatuses, $statusWeights);

                // Generate random date within the month
                $daysInMonth = $currentDate->daysInMonth;
                $day = rand(1, $daysInMonth);
                $hour = rand(8, 22);
                $minute = rand(0, 59);
                $second = rand(0, 59);

                $transactionDate = Carbon::create(
                    $currentDate->year,
                    $currentDate->month,
                    $day,
                    $hour,
                    $minute,
                    $second
                );

                $amount = $rak->harga_sewa_perbulan;

                // Generate unique order ID
                $orderId = 'ORDER-' . $transactionDate->format('YmdHis') . $rak->id . $customer->id . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

                Transaction::create([
                    'order_id' => $orderId,
                    'user_id' => $customer->id,
                    'rak_id' => $rak->id,
                    'amount' => $amount,
                    'transaction_status' => $status,
                    'snap_token' => 'snap_' . $orderId,
                    'payment_type' => collect($paymentTypes)->random(),
                    'transaction_time' => $transactionDate,
                    'fraud_status' => rand(1, 10) <= 9 ? 'accept' : 'challenge', // 90% accept
                    'midtrans_response' => [
                        'status_code' => '200',
                        'status_message' => 'Success',
                        'transaction_id' => $orderId,
                        'order_id' => $orderId,
                        'gross_amount' => (string)$amount,
                        'payment_type' => collect($paymentTypes)->random(),
                        'transaction_time' => $transactionDate->toISOString(),
                        'transaction_status' => $status,
                        'fraud_status' => rand(1, 10) <= 9 ? 'accept' : 'challenge',
                    ]
                ]);

                $totalTransactions++;
            }

            $currentDate->addMonth();
        }

        $this->command->info("✅ Successfully created {$totalTransactions} transaction records.");
    }

    /**
     * Get random element with weighted probability
     */
    private function weightedRandom(array $items, array $weights): mixed
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
