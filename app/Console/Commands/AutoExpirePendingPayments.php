<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;

class AutoExpirePendingPayments extends Command
{
    protected $signature = 'payments:auto-expire';
    protected $description = 'Auto expire pending payments after 24 hours';

    public function handle()
    {
        $expiredCount = Transaction::where('transaction_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->update(['transaction_status' => 'expired']);
            
        $this->info("{$expiredCount} pending payments expired.");
        return Command::SUCCESS;
    }
}