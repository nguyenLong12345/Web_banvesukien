<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * User's tickets list. Filters: tstatus, estatus.
     * Maps: pages/my_tickets.php
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tstatus = $request->get('tstatus', 'all');
        $estatus = $request->get('estatus', 'all');

        $statusMap = [
            'upcoming' => 'Chưa diễn ra',
            'active' => 'Đang diễn ra',
            'ended' => 'Đã kết thúc',
            'cancelled' => 'Đã hủy',
        ];

        $query = DB::table('payments as p')
            ->select(
                'p.payment_id', 'p.user_id', 'p.payment_at', 'p.method', 'p.amount',
                'p.fullname', 'p.email', 'p.phone',
                'o.event_id', 'o.quantity', 't.seat_id', 's.seat_number',
                'e.event_name', 'e.start_time', 'e.event_img', 'e.eStatus', 't.tStatus'
            )
            ->leftJoin('orders as o', 'p.payment_id', '=', 'o.payment_id')
            ->leftJoin('tickets as t', 'o.order_id', '=', 't.order_id')
            ->leftJoin('seats as s', 't.seat_id', '=', 's.seat_id')
            ->leftJoin('events as e', 'o.event_id', '=', 'e.event_id')
            ->where('p.user_id', $user->user_id);

        if ($estatus !== 'all' && isset($statusMap[$estatus])) {
            $query->where('e.eStatus', $statusMap[$estatus]);
        }

        if ($tstatus !== 'all') {
            $query->where('t.tStatus', $tstatus);
        }

        $query->orderBy($estatus === 'all' ? 'e.start_time' : 'p.payment_at', $estatus === 'all' ? 'asc' : 'desc');

        $orders = $query->get();

        // Also include cancelled payments that have no orders/tickets
        if ($tstatus === 'all' || $tstatus === 'Đã hủy') {
            $cancelledPayments = DB::table('payments as p')
                ->select(
                    'p.payment_id', 'p.user_id', 'p.payment_at', 'p.method', 'p.amount',
                    'p.fullname', 'p.email', 'p.phone', 'p.meta_event_id', 'p.meta_seats',
                    'e.event_name', 'e.start_time', 'e.event_img', 'e.eStatus'
                )
                ->leftJoin('events as e', 'p.meta_event_id', '=', 'e.event_id')
                ->where('p.user_id', $user->user_id)
                ->where('p.pStatus', 'cancel')
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('orders')
                        ->whereColumn('orders.payment_id', 'p.payment_id');
                })
                ->orderByDesc('p.payment_at')
                ->get()
                ->map(function ($item) {
                    $item->event_id = $item->meta_event_id;
                    $item->quantity = 0;
                    $item->seat_id = null;
                    $item->seat_number = $item->meta_seats ?? '-';
                    $item->tStatus = 'Đã hủy';
                    return $item;
                });

            $orders = $orders->merge($cancelledPayments);
        }

        return view('tickets.index', [
            'orders' => $orders,
            'tstatus' => $tstatus,
            'estatus' => $estatus,
        ]);
    }
}
