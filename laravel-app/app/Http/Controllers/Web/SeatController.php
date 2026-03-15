<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeatController extends Controller
{
    /**
     * Seat selection page. Requires auth + session[booking].
     * Maps: pages/select_seats.php
     */
    public function selectSeats(Event $event): View|JsonResponse
    {
        $booking = session('booking', []);

        if (empty($booking) || ($booking['event_id'] ?? null) !== $event->event_id) {
            return redirect()->route('events.payment', $event)
                ->with('error', 'Vui lòng xác nhận thông tin đặt vé trước khi chọn ghế.');
        }

        $seats = $event->seats()
            ->orderByRaw('LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)')
            ->get();

        return view('events.select-seats', [
            'event' => $event,
            'booking' => $booking,
            'seats' => $seats,
        ]);
    }

    /**
     * AJAX: Get seats for event (JSON).
     * Maps: assets/ajax/get_seats.php
     */
    public function getSeats(Event $event): JsonResponse
    {
        $seats = $event->seats()
            ->orderByRaw('LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)')
            ->get();

        return response()->json([
            'seats' => $seats->map(fn ($s) => [
                'seat_id' => $s->seat_id,
                'seat_number' => $s->seat_number,
                'seat_type' => $s->seat_type,
                'seat_price' => (float) $s->seat_price,
                'sStatus' => $s->sStatus,
            ]),
        ]);
    }
}
