<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Seat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Event\EventStatusService;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        protected EventStatusService $eventStatusService
    ) {
        $this->eventStatusService->syncAllEventStatuses();
    }
    public function index(Request $request): View
    {
        $status = $request->get('status', 'upcoming');
        $search = $request->get('search', '');
        $filterDate = $request->get('filter_date', '');
        $viewEventId = $request->get('view');

        $statusValue = match ($status) {
            'ended' => 'Đã kết thúc',
            'active' => 'Đang diễn ra',
            default => 'Chưa diễn ra',
        };

        $query = Event::where('eStatus', $statusValue);

        if ($request->filled('search')) {
            $query->where('event_name', 'like', "%{$search}%");
        }

        if ($request->filled('filter_date')) {
            $query->whereDate('start_time', $filterDate);
        }

        $events = $query->orderByDesc('start_time')->get();

        $eventIdsWithBookedSeats = Seat::where('sStatus', '!=', 'Còn trống')
            ->distinct()
            ->pluck('event_id')
            ->toArray();

        $maxId = Event::query()
            ->selectRaw('MAX(CAST(SUBSTRING(event_id, 2) AS UNSIGNED)) as max_id')
            ->value('max_id');
        $nextEventId = 'E0' . str_pad((int) $maxId + 1, 2, '0', STR_PAD_LEFT);

        $selectedEvent = null;
        $seats = [];

        if ($viewEventId) {
            $selectedEvent = $events->firstWhere('event_id', $viewEventId);
            if ($selectedEvent) {
                $seats = Seat::where('event_id', $viewEventId)->orderByRaw('LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)')->get();
            }
        }

        return view('admin.events.index', compact(
            'events', 'status', 'search', 'filterDate', 'eventIdsWithBookedSeats',
            'nextEventId', 'selectedEvent', 'seats'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|string',
            'event_name' => 'required|string|max:255',
            'location' => 'required|string',
            'start_time' => 'required|date',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'total_seats' => 'required|in:50,100',
            'event_type' => 'required|in:music,art,visit,tournament',
            'eStatus' => 'required|in:Chưa diễn ra,Đang diễn ra,Đã kết thúc,Đã bị hủy',
            'event_img' => 'nullable|image',
            'event_img_link' => 'nullable|string',
            'old_event_img' => 'nullable|string',
        ]);

        $imgPath = $validated['old_event_img'] ?? '';
        if ($request->hasFile('event_img')) {
            $imgPath = $request->file('event_img')->store('events', 'public');
        } elseif (!empty($validated['event_img_link'])) {
            $imgPath = $validated['event_img_link'];
        }
        $imgPath = $imgPath ?: 'gaudeptrai2.jpg';

        Event::create([
            'event_id' => $validated['event_id'],
            'event_name' => $validated['event_name'],
            'location' => $validated['location'],
            'start_time' => $validated['start_time'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'total_seats' => $validated['total_seats'],
            'event_type' => $validated['event_type'],
            'eStatus' => $validated['eStatus'],
            'event_img' => $imgPath,
        ]);

        $this->generateSeatsForEvent($validated['event_id'], (int) $validated['total_seats'], (float) $validated['price']);

        return redirect()->route('admin.events.index', ['status' => 'upcoming'])->with('success', 'Thêm sự kiện thành công.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'location' => 'required|string',
            'start_time' => 'required|date',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'total_seats' => 'required|in:50,100',
            'event_type' => 'required|in:music,art,visit,tournament',
            'eStatus' => 'required|in:Chưa diễn ra,Đang diễn ra,Đã kết thúc,Đã bị hủy',
            'event_img' => 'nullable|image',
            'event_img_link' => 'nullable|string',
            'old_event_img' => 'nullable|string',
        ]);

        $imgPath = $validated['old_event_img'] ?? $event->event_img;
        if ($request->hasFile('event_img')) {
            $file = $request->file('event_img');
            $imgPath = $file->store('events', 'public');
        } elseif (!empty($validated['event_img_link'])) {
            $imgPath = $validated['event_img_link'];
        }

        $event->update([
            'event_name' => $validated['event_name'],
            'location' => $validated['location'],
            'start_time' => $validated['start_time'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'total_seats' => $validated['total_seats'],
            'event_type' => $validated['event_type'],
            'eStatus' => $validated['eStatus'],
            'event_img' => $imgPath,
        ]);

        return redirect()->route('admin.events.index', ['status' => 'upcoming'])->with('success', 'Cập nhật sự kiện thành công.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $hasBooking = $event->seats()->where('sStatus', '!=', 'Còn trống')->exists();
        if ($hasBooking) {
            return redirect()->route('admin.events.index', ['status' => 'upcoming'])
                ->with('error', 'Không thể xóa sự kiện vì đã có người đặt chỗ.');
        }
        $event->delete();
        return redirect()->route('admin.events.index', ['status' => 'upcoming'])->with('success', 'Đã xóa sự kiện.');
    }

    public function generateSeats(Event $event): RedirectResponse
    {
        Seat::where('event_id', $event->event_id)->delete();
        $this->generateSeatsForEvent($event->event_id, (int) $event->total_seats, (float) $event->price);
        return redirect()->back()->with('success', 'Đã tạo ghế cho sự kiện.');
    }

    private function generateSeatsForEvent(string $eventId, int $totalSeats, float $price = 0): void
    {
        $rows = range('A', 'Z');
        $seatsPerRow = 10;
        $numRows = (int) ceil($totalSeats / $seatsPerRow);
        $vipRows = $totalSeats <= 50 ? 1 : 2; // Row A is VIP for 50 seats, A-B for 100 seats

        $seatCount = 0;
        for ($r = 0; $r < $numRows && $seatCount < $totalSeats; $r++) {
            $rowLetter = $rows[$r];
            $isVip = $r < $vipRows;
            $seatType = $isVip ? 'vip' : 'normal';
            $seatPrice = $isVip ? ($price * 1.5) : $price;

            for ($i = 1; $i <= $seatsPerRow && $seatCount < $totalSeats; $i++) {
                $seatNumber = $rowLetter . $i;
                Seat::create([
                    'seat_id' => 'S' . substr(uniqid(), -8),
                    'event_id' => $eventId,
                    'seat_number' => $seatNumber,
                    'sStatus' => 'Còn trống',
                    'seat_type' => $seatType,
                    'seat_price' => $seatPrice,
                ]);
                $seatCount++;
            }
        }
    }
}
