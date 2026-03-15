<?php

namespace App\Services\Booking;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected \App\Services\Payment\VNPayService $vnpayService,
    ) {
    }

    /**
     * Store pending booking in session.
     *
     * @param  array{event_id: string, type: string, fullname: string, email: string, phone: string, method: string}  $data
     */
    public function storeInSession(array $data): void
    {
        $booking = [
            'event_id' => $data['event_id'],
            'event_type' => $data['type'],
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'payment_method' => $data['method'],
        ];
        session(['booking' => $booking]);
    }

    /**
     * Create payment record (pending) and return VNPay redirect URL.
     *
     * @param  array<int|string>  $selectedSeatIds
     */
    public function createPendingPaymentAndGetRedirectUrl(
        User $user,
        array $selectedSeatIds,
        float $totalAmount,
        string $eventId
    ): string {
        $booking = session('booking', []);
        $fullname = $booking['fullname'] ?? $user->fullname;
        $email = $booking['email'] ?? $user->email;
        $phone = $booking['phone'] ?? '';

        $txnRef = $this->vnpayService->generateTxnRef();
        $paymentId = $this->generatePaymentId();

        // Bug 5 fix: set payment_at when creating payment
        Payment::create([
            'payment_id' => $paymentId,
            'user_id' => $user->user_id,
            'payment_at' => now(),
            'method' => 'vnpay',
            'amount' => $totalAmount,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'pStatus' => 'pending',
            'vnp_transaction_no' => $txnRef,
        ]);

        // Lock selected seats as "Đang giữ" so others can't select them
        Seat::whereIn('seat_id', $selectedSeatIds)
            ->where('sStatus', 'Còn trống')
            ->update(['sStatus' => 'Đang giữ']);

        // Store selected seats + event_id linked to payment for DB lookup on return
        session(['payment' => [
            'payment_id' => $paymentId,
            'vnp_transaction_no' => $txnRef,
            'selected_seats' => $selectedSeatIds,
            'total_amount' => $totalAmount,
            'event_id' => $eventId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
        ]]);

        // Also persist selected_seats and event_id to the payment record
        // so we can look them up from DB when VNPay redirects back
        Payment::where('payment_id', $paymentId)->update([
            'meta_seats' => json_encode($selectedSeatIds),
            'meta_event_id' => $eventId,
        ]);

        $url = $this->vnpayService->createPaymentUrl([
            'amount' => $totalAmount,
            'orderInfo' => 'Thanh toán vé sự kiện: ' . $txnRef,
            'userIp' => request()->ip(),
            'txnRef' => $txnRef,
        ]);

        return $url;
    }

    /**
     * Process successful VNPay callback: update payment, create order & tickets.
     * Uses DB-stored payment data instead of session (session may be lost after redirect).
     */
    public function completeBookingFromPayment(Payment $payment, string $vnpTransactionNo): void
    {
        DB::transaction(function () use ($payment, $vnpTransactionNo) {
            $paymentId = $payment->payment_id;
            $selectedSeats = json_decode($payment->meta_seats ?? '[]', true);
            $eventId = $payment->meta_event_id;

            // If meta fields are empty, try session as fallback
            if (empty($selectedSeats)) {
                $paymentSession = session('payment', []);
                $selectedSeats = $paymentSession['selected_seats'] ?? [];
                $eventId = $eventId ?: ($paymentSession['event_id'] ?? null);
            }

            if (empty($selectedSeats) || ! $eventId) {
                throw new \RuntimeException('Missing seat or event data for payment: ' . $paymentId);
            }

            $payment->update([
                'pStatus' => 'paid',
                'vnp_transaction_no' => $vnpTransactionNo,
                'payment_time' => now(),
            ]);

            Seat::whereIn('seat_id', $selectedSeats)->update(['sStatus' => 'Đã đặt']);

            $orderId = $this->generateOrderId();
            $quantity = count($selectedSeats);

            Order::create([
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'event_id' => $eventId,
                'created_at' => now(),
                'quantity' => $quantity,
            ]);

            $ticketStartNum = $this->getNextTicketNumber();
            foreach ($selectedSeats as $index => $seatId) {
                $ticketId = 'T0' . ($ticketStartNum + $index);
                Ticket::create([
                    'ticket_id' => $ticketId,
                    'order_id' => $orderId,
                    'seat_id' => $seatId,
                    'tStatus' => 'Thành công',
                ]);
            }
        });
    }

    /**
     * Legacy method kept for backward compatibility.
     *
     * @param  array{payment_id: string, selected_seats: array, event_id: string, ...}  $paymentSession
     */
    public function completeBooking(User $user, array $paymentSession, string $vnpTransactionNo): void
    {
        $payment = Payment::where('payment_id', $paymentSession['payment_id'])
            ->where('user_id', $user->user_id)
            ->where('pStatus', 'pending')
            ->first();

        if ($payment) {
            // Ensure meta fields are populated
            if (empty($payment->meta_seats)) {
                $payment->update([
                    'meta_seats' => json_encode($paymentSession['selected_seats'] ?? []),
                    'meta_event_id' => $paymentSession['event_id'] ?? null,
                ]);
                $payment->refresh();
            }
            $this->completeBookingFromPayment($payment, $vnpTransactionNo);
        }
    }

    /**
     * Cancel pending payment on failure/cancel.
     * Bug 6 fix: also release held/booked seats back to "Còn trống".
     */
    public function cancelPendingPayment(string $userId, string $paymentId): void
    {
        $payment = Payment::where('user_id', $userId)
            ->where('payment_id', $paymentId)
            ->where('pStatus', 'pending')
            ->first();

        if (! $payment) {
            return;
        }

        $payment->update(['pStatus' => 'cancel']);

        // Release seats: find seats via meta_seats or session
        $selectedSeats = json_decode($payment->meta_seats ?? '[]', true);

        if (empty($selectedSeats)) {
            $paymentSession = session('payment', []);
            $selectedSeats = $paymentSession['selected_seats'] ?? [];
        }

        if (! empty($selectedSeats)) {
            Seat::whereIn('seat_id', $selectedSeats)
                ->whereIn('sStatus', ['Đang giữ', 'Đã đặt'])
                ->update(['sStatus' => 'Còn trống']);
        }
    }

    protected function generatePaymentId(): string
    {
        $max = Payment::selectRaw("CAST(SUBSTRING(payment_id, 3) AS UNSIGNED) as num")
            ->where('payment_id', 'like', 'P0%')
            ->orderByRaw('CAST(SUBSTRING(payment_id, 3) AS UNSIGNED) DESC')
            ->value('num');

        return 'P0' . (($max ?? 0) + 1);
    }

    /**
     * Bug 9 fix: use MAX(id) instead of COUNT() for safe ID generation.
     */
    protected function generateOrderId(): string
    {
        $max = Order::selectRaw("CAST(SUBSTRING(order_id, 3) AS UNSIGNED) as num")
            ->where('order_id', 'like', 'O0%')
            ->orderByRaw('CAST(SUBSTRING(order_id, 3) AS UNSIGNED) DESC')
            ->value('num');

        return 'O0' . (($max ?? 0) + 1);
    }

    protected function getNextTicketNumber(): int
    {
        $max = Ticket::selectRaw("CAST(SUBSTRING(ticket_id, 3) AS UNSIGNED) as num")
            ->where('ticket_id', 'like', 'T0%')
            ->orderByRaw('CAST(SUBSTRING(ticket_id, 3) AS UNSIGNED) DESC')
            ->value('num');

        return ($max ?? 0) + 1;
    }
}
