<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    /**
     * Confirm booking - save to session, redirect to select_seats.
     * Maps: process/confirm_booking.php
     * POST: event_id, type, fullname, email, phone, method
     */
    public function confirm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|string|exists:events,event_id',
            'type' => 'required|string',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'method' => 'required|string|in:vnpay,bank',
        ], [
            'event_id.required' => 'Thiếu thông tin sự kiện.',
            'event_id.exists' => 'Sự kiện không tồn tại.',
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
        ]);

        $this->bookingService->storeInSession($validated);

        $eventId = $validated['event_id'];

        return redirect()->route('events.select-seats', $eventId);
    }
}
