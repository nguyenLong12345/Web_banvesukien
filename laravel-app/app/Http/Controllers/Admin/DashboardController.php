<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalOrders = Order::count();
        $totalTickets = Ticket::where('tStatus', 'Thành công')->count();
        $totalPaids = Payment::where('pStatus', 'paid')->sum('amount');

        $revenueDate = $request->get('revenue_date', now()->format('Y-m-d'));
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $dailyTotal = Payment::where('pStatus', 'paid')
            ->whereRaw('DATE(COALESCE(payment_time, payment_at)) = ?', [$revenueDate])
            ->sum('amount');

        $filteredTotal = 0;
        if ($fromDate && $toDate) {
            $filteredTotal = Payment::where('pStatus', 'paid')
                ->whereRaw('DATE(COALESCE(payment_time, payment_at)) BETWEEN ? AND ?', [$fromDate, $toDate])
                ->sum('amount');
        }

        $currentYear = now()->year;
        $monthlyRevenue = [];
        $monthData = Payment::where('pStatus', 'paid')
            ->whereRaw('YEAR(COALESCE(payment_time, payment_at)) = ?', [$currentYear])
            ->selectRaw('MONTH(COALESCE(payment_time, payment_at)) as m, SUM(amount) as total')
            ->groupByRaw('MONTH(COALESCE(payment_time, payment_at))')
            ->pluck('total', 'm')
            ->toArray();
        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenue[$m] = (int) (isset($monthData[$m]) ? $monthData[$m] : 0);
        }

        $quarterRevenue = [
            'Quý 1' => $monthlyRevenue[1] + $monthlyRevenue[2] + $monthlyRevenue[3],
            'Quý 2' => $monthlyRevenue[4] + $monthlyRevenue[5] + $monthlyRevenue[6],
            'Quý 3' => $monthlyRevenue[7] + $monthlyRevenue[8] + $monthlyRevenue[9],
            'Quý 4' => $monthlyRevenue[10] + $monthlyRevenue[11] + $monthlyRevenue[12],
        ];

        $startOfWeek = now()->startOfWeek();
        $weeklyRevenueLabels = [];
        $weeklyRevenueData = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $weeklyRevenueLabels[] = $day->format('D d/m');
            $total = Payment::where('pStatus', 'paid')
                ->whereRaw('DATE(COALESCE(payment_time, payment_at)) = ?', [$day->format('Y-m-d')])
                ->sum('amount');
            $weeklyRevenueData[] = (int) $total;
        }

        $startYear = $currentYear - 4;
        $yearRevenue = [];
        for ($y = $startYear; $y <= $currentYear; $y++) {
            $yearRevenue[$y] = (int) Payment::where('pStatus', 'paid')
                ->whereRaw('YEAR(COALESCE(payment_time, payment_at)) = ?', [$y])
                ->sum('amount');
        }

        // 1. Dữ liệu cho biểu đồ tròn (Tỉ lệ loại sự kiện theo số vé bán được)
        $eventTypeDataRaw = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.order_id')
            ->join('events', 'orders.event_id', '=', 'events.event_id')
            ->where('tickets.tStatus', 'Thành công')
            ->select('events.event_type', DB::raw('count(*) as total_tickets'))
            ->groupBy('events.event_type')
            ->get();

        $eventTypeLabels = [];
        $eventTypeData = [];
        $typeNames = [
            'music' => 'Âm nhạc',
            'art' => 'Nghệ thuật',
            'visit' => 'Tham quan',
            'tournament' => 'Giải đấu'
        ];

        foreach ($eventTypeDataRaw as $item) {
            $eventTypeLabels[] = $typeNames[$item->event_type] ?? $item->event_type;
            $eventTypeData[] = $item->total_tickets;
        }

        // 2. Dữ liệu cho Top 5 sự kiện bán chạy nhất
        $topEventsRaw = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.order_id')
            ->join('events', 'orders.event_id', '=', 'events.event_id')
            ->where('tickets.tStatus', 'Thành công')
            ->select('events.event_name', DB::raw('count(*) as total_tickets'))
            ->groupBy('events.event_id', 'events.event_name')
            ->orderByDesc('total_tickets')
            ->limit(5)
            ->get();

        $topEventLabels = [];
        $topEventData = [];
        foreach ($topEventsRaw as $item) {
            $topEventLabels[] = strlen($item->event_name) > 30 ? substr($item->event_name, 0, 30) . '...' : $item->event_name;
            $topEventData[] = $item->total_tickets;
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEvents', 'totalOrders', 'totalPaids', 'totalTickets',
            'revenueDate', 'dailyTotal', 'filteredTotal', 'fromDate', 'toDate',
            'monthlyRevenue', 'quarterRevenue', 'weeklyRevenueLabels', 'weeklyRevenueData',
            'yearRevenue', 'currentYear', 'startYear',
            'eventTypeLabels', 'eventTypeData', 'topEventLabels', 'topEventData'
        ));
    }
}
