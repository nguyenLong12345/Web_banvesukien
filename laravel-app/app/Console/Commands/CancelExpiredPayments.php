<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CancelExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel pending payments older than 5 minutes and release held seats.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Booking\BookingService $bookingService)
    {
        $expiredPayments = \App\Models\Payment::where('pStatus', 'pending')
            ->where('payment_at', '<', now()->subMinutes(5))
            ->get();

        $count = $expiredPayments->count();
        if ($count === 0) {
            $this->info('No expired pending payments found.');
            return;
        }

        foreach ($expiredPayments as $payment) {
            try {
                $bookingService->cancelPendingPayment($payment->user_id, $payment->payment_id);
                $this->info("Cancelled payment: {$payment->payment_id}");
            } catch (\Exception $e) {
                $this->error("Failed to cancel payment {$payment->payment_id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully cancelled {$count} expired pending payment(s).");
    }
}
