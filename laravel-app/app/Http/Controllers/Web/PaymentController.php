<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    /**
     * Prepare VNPay request and redirect to gateway.
     * Maps: pages/vn_pay_redirect.php
     * POST: selected_seats (JSON), total_amount
     */
    public function vnpayRedirect(Request $request): RedirectResponse
    {
        $selectedSeats = $request->input('selected_seats');
        if (is_string($selectedSeats)) {
            $selectedSeats = json_decode($selectedSeats, true) ?: [];
        }
        $totalAmount = (float) $request->input('total_amount', 0);

        if (empty($selectedSeats) || $totalAmount <= 0) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ. Vui lòng chọn ghế và kiểm tra tổng tiền.');
        }

        $user = auth()->user();
        $booking = session('booking', []);

        if (! $user || empty($booking)) {
            return redirect()->route('home')->with('error', 'Bạn cần đăng nhập và chọn sự kiện trước khi thanh toán.');
        }

        $eventId = $booking['event_id'] ?? null;
        if (! $eventId) {
            return redirect()->route('home')->with('error', 'Thông tin đặt vé không hợp lệ.');
        }

        try {
            $url = $this->bookingService->createPendingPaymentAndGetRedirectUrl(
                $user,
                $selectedSeats,
                $totalAmount,
                $eventId
            );

            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('VNPay redirect error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Lỗi khi tạo yêu cầu thanh toán. Vui lòng thử lại.');
        }
    }

    /**
     * VNPay callback - update payment status, create orders/tickets.
     * Maps: pages/vnpay_return.php
     *
     * NOTE: This route is a GET without auth middleware because VNPay redirects
     * the browser here. We look up payment by vnp_TxnRef instead of relying
     * on session, since session/cookie may not survive the redirect.
     */
    public function vnpayReturn(Request $request): RedirectResponse
    {
        $vnpay = app(\App\Services\Payment\VNPayService::class);
        $queryParams = $request->query();

        // 1. Verify the secure hash from VNPay
        if (! $vnpay->verifyReturnHash($queryParams)) {
            Log::warning('VNPay return: hash verification failed', ['params' => $queryParams]);

            return redirect()->route('home')->with('error', 'Xác thực thanh toán thất bại. Chữ ký không hợp lệ.');
        }

        // 2. Look up payment by txnRef from DB instead of session (session may be lost after redirect)
        $txnRef = $request->query('vnp_TxnRef', '');
        $payment = Payment::where('vnp_transaction_no', $txnRef)
            ->where('pStatus', 'pending')
            ->first();

        if (! $payment) {
            Log::warning('VNPay return: payment not found for txnRef', ['txnRef' => $txnRef]);

            return redirect()->route('home')->with('error', 'Không tìm thấy giao dịch hoặc phiên đã hết hạn.');
        }

        $responseCode = $request->query('vnp_ResponseCode', '');
        $transactionStatus = $request->query('vnp_TransactionStatus', '');
        $vnpTransactionNo = $request->query('vnp_TransactionNo', '');

        if ($responseCode === '00' && $transactionStatus === '00') {
            // 3a. Payment success - complete booking using DB-based lookup
            $this->bookingService->completeBookingFromPayment($payment, $vnpTransactionNo);

            // Clean up session if still available
            session()->forget(['payment', 'booking']);

            return redirect()->route('tickets.index')->with('success', 'Đặt vé thành công!');
        }

        // 3b. Payment failed or cancelled - cancel the pending payment
        $this->bookingService->cancelPendingPayment($payment->user_id, $payment->payment_id);

        // Clean up session if still available
        session()->forget(['payment', 'booking']);

        return redirect()->route('home')->with('error', 'Đã hủy đặt vé.');
    }
}
