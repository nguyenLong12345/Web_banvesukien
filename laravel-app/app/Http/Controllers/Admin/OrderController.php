<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterDate = $request->get('filter_date', '');
        $selectedOrderId = $request->get('order_id');

        $query = Order::with(['event', 'payment']);

        if ($request->filled('search')) {
            $query->whereHas('event', fn ($q) => $q->where('event_name', 'like', "%{$search}%"));
        }
        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $filterDate);
        }

        $orders = $query->orderByDesc('created_at')->get();

        $ticketDetails = collect();
        if ($selectedOrderId) {
            $ticketDetails = Ticket::with(['seat', 'order.event', 'order.payment'])
                ->where('order_id', $selectedOrderId)
                ->get();
        }

        return view('admin.orders.index', compact('orders', 'search', 'filterDate', 'selectedOrderId', 'ticketDetails'));
    }

    public function updateTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_id' => 'required|string',
            'seat_id' => 'required|string',
            'new_status' => 'required|in:Thành công,Đã hủy',
            'order_id' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);
        $ticket->update(['tStatus' => $validated['new_status']]);

        if ($validated['new_status'] === 'Đã hủy') {
            Seat::where('seat_id', $validated['seat_id'])->update(['sStatus' => 'Còn trống']);
        } else {
            Seat::where('seat_id', $validated['seat_id'])->update(['sStatus' => 'Đã đặt']);
        }

        return redirect()->route('admin.orders.index', ['order_id' => $validated['order_id']])->with('success', 'Cập nhật trạng thái vé thành công.');
    }

    public function history(Request $request): View
    {
        $status = $request->get('status', 'paid');
        $search = $request->get('search', '');
        $filterDate = $request->get('filter_date', '');

        $query = Payment::query();

        if (in_array($status, ['paid', 'pending', 'cancel'])) {
            $query->where('pStatus', $status);
        }
        if ($request->filled('search')) {
            $query->where('fullname', 'like', "%{$search}%");
        }

        if ($request->filled('filter_date')) {
            $query->whereDate('payment_at', $filterDate);
        }

        $payments = $query->orderByDesc('payment_at')->get();

        return view('admin.history.index', compact('payments', 'status', 'search', 'filterDate'));
    }
}
