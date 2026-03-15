<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateEventDatesSeeder extends Seeder
{
    /**
     * Update event dates to future dates (2026)
     */
    public function run(): void
    {
        echo "→ Updating event dates to 2026...\n";

        // Get all events
        $events = DB::table('events')->get();

        foreach ($events as $event) {
            // Parse the original date
            $originalDate = Carbon::parse($event->start_time);
            
            // Change year to 2026 and keep month/day/time
            $newDate = Carbon::create(
                2026,
                $originalDate->month,
                $originalDate->day,
                $originalDate->hour,
                $originalDate->minute,
                $originalDate->second
            );
            
            // If the date is in the past (before today), add some months
            if ($newDate->isPast()) {
                $newDate->addMonths(3);
            }
            
            // Update the event
            DB::table('events')
                ->where('event_id', $event->event_id)
                ->update([
                    'start_time' => $newDate,
                    'eStatus' => 'Chưa diễn ra'
                ]);
        }

        echo "✓ Updated " . $events->count() . " events\n";
        
        // Show sample of updated events
        echo "\n=== SAMPLE UPDATED EVENTS ===\n";
        $samples = DB::table('events')
            ->select('event_id', 'event_name', 'start_time', 'eStatus')
            ->limit(5)
            ->get();
        
        foreach ($samples as $event) {
            echo "- {$event->event_name}\n";
            echo "  Date: {$event->start_time}\n";
            echo "  Status: {$event->eStatus}\n\n";
        }
        
        echo "✓ All events are now in the future!\n";
    }
}
