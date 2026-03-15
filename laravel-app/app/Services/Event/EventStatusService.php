<?php

namespace App\Services\Event;

use App\Models\Event;

class EventStatusService
{
    /**
     * Update all events' eStatus based on start_time and duration.
     */
    public function syncAllEventStatuses(): void
    {
        $events = Event::where('eStatus', '!=', 'Đã kết thúc')->get();

        foreach ($events as $event) {
            $event->updateStatusFromTime();
        }
    }
}
