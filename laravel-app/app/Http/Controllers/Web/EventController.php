<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Event\EventStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        protected EventStatusService $eventStatusService
    ) {
        $this->eventStatusService->syncAllEventStatuses();
    }

    /**
     * Home page - event slider, featured events, music/visit sections.
     * Maps: index.php, pages/home.php
     */
    public function home(): View
    {

        $sliderEvents = Event::where('eStatus', 'Chưa diễn ra')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $specialEvents = Event::where('eStatus', 'Chưa diễn ra')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(12)
            ->get();

        $popularEvents = Event::select('events.event_id', 'events.event_img', 'events.event_name')
            ->join(DB::raw('(SELECT event_id, COUNT(*) AS total FROM orders GROUP BY event_id ORDER BY total DESC) AS pt'), 'pt.event_id', '=', 'events.event_id')
            ->where('events.eStatus', 'Chưa diễn ra')
            ->orderByDesc('pt.total')
            ->limit(6)
            ->get();

        $excludedIds = $popularEvents->pluck('event_id')->toArray();
        $randomEvents = Event::where('eStatus', 'Chưa diễn ra')
            ->when(count($excludedIds) > 0, fn ($q) => $q->whereNotIn('event_id', $excludedIds))
            ->inRandomOrder()
            ->limit(6 - $popularEvents->count())
            ->get(['event_id', 'event_img', 'event_name']);

        $featuredEvents = $popularEvents->merge($randomEvents)->take(6);

        $musicEvents = Event::where('event_type', 'music')
            ->where('eStatus', 'Chưa diễn ra')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        $visitEvents = Event::where('event_type', 'visit')
            ->where('eStatus', 'Chưa diễn ra')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        $artEvents = Event::where('event_type', 'art')
            ->where('eStatus', 'Chưa diễn ra')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        $tournamentEvents = Event::where('event_type', 'tournament')
            ->where('eStatus', 'Chưa diễn ra')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        return view('events.home', [
            'sliderEvents' => $sliderEvents,
            'specialEvents' => $specialEvents,
            'featuredEvents' => $featuredEvents,
            'musicEvents' => $musicEvents,
            'visitEvents' => $visitEvents,
            'artEvents' => $artEvents,
            'tournamentEvents' => $tournamentEvents,
        ]);
    }

    /**
     * Search events by query and time_filter.
     * Maps: pages/search.php
     */
    public function search(Request $request): View
    {
        $query = $request->get('query', '');
        $timeFilter = $request->get('time_filter', '');
        $location = $request->get('location', '');

        $q = Event::where('eStatus', 'Chưa diễn ra');

        if (! empty(trim($query))) {
            $q->where('event_name', 'like', '%' . $query . '%');
        }

        if ($timeFilter === 'week') {
            $q->whereRaw('WEEK(start_time) = WEEK(CURDATE()) AND YEAR(start_time) = YEAR(CURDATE())');
        } elseif ($timeFilter === 'month') {
            $q->whereRaw('MONTH(start_time) = MONTH(CURDATE()) AND YEAR(start_time) = YEAR(CURDATE())');
        }

        if (! empty($location)) {
            $this->applyLocationFilter($q, $location);
        }

        $results = $q->orderBy('start_time')->get();

        return view('events.search', [
            'query' => $query,
            'timeFilter' => $timeFilter,
            'results' => $results,
        ]);
    }

    /**
     * Return JSON data for the AJAX autocompelte dropdown on the header search bar.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query', '');

        if (empty(trim($query))) {
            return response()->json([]);
        }

        $results = Event::where('eStatus', 'Chưa diễn ra')
            ->where('event_name', 'LIKE', '%' . $query . '%')
            ->orderBy('start_time')
            ->limit(5)
            ->get(['event_id', 'event_name', 'event_img', 'start_time']);

        $suggestions = $results->map(function ($event) {
            return [
                'name' => $event->event_name,
                'image' => $event->image_url,
                'date' => \Carbon\Carbon::parse($event->start_time)->format('d/m/Y'),
                'url' => route('events.payment', $event->event_id)
            ];
        });

        return response()->json($suggestions);
    }

    /**
     * Filter events by type (all, music, art, visit, tournament).
     * Maps: pages/event_type.php
     */
    public function eventType(string $type): View
    {
        $eventTypeMap = [
            'music' => 'Âm nhạc',
            'visit' => 'Tham quan',
            'tournament' => 'Giải đấu',
            'art' => 'Văn hóa nghệ thuật',
            'all' => 'Tất cả',
        ];
        $eventTypeDisplay = $eventTypeMap[$type] ?? 'Mới nhất';
        $location = request('location');

        $today = now()->format('Y-m-d');

        $q = Event::where('eStatus', 'Chưa diễn ra');

        if ($type !== 'all') {
            $q->where('event_type', $type)
              ->where('start_time', '>=', $today);
        }

        if (! empty($location)) {
            $this->applyLocationFilter($q, $location);
        }

        $results = $q->orderBy('start_time')->get();

        $mainEvent = $results->first();

        return view('events.event-type', [
            'type' => $type,
            'eventTypeDisplay' => $eventTypeDisplay,
            'results' => $results,
            'mainEvent' => $mainEvent,
        ]);
    }

    /**
     * Event detail & buy button.
     * Maps: pages/payment.php
     */
    public function payment(Event $event): View
    {
        if (auth()->check()) {
            $booking = array_merge(session('booking', []), [
                'event_id' => $event->event_id,
                'event_name' => $event->event_name,
            ]);
            session(['booking' => $booking]);
        }

        $seats = $event->seats()
            ->orderByRaw('LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)')
            ->get();

        return view('events.payment', [
            'event' => $event,
            'seats' => $seats,
        ]);
    }

    /**
     * Map location code to city name for LIKE queries
     */
    private function applyLocationFilter($query, string $locationCode)
    {
        $map = [
            'HN' => 'Hà Nội',
            'HCM' => 'Hồ Chí Minh',
            'DL' => 'Đà Lạt',
            'QN' => 'Quảng Ninh',
            'HUE' => 'Huế',
            'QNA' => 'Quảng Nam',
            'DN' => 'Đà Nẵng',
        ];

        if (isset($map[$locationCode])) {
            $query->where('location', 'LIKE', '%' . $map[$locationCode] . '%');
        }
    }
}
