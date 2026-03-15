<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'event_name',
        'start_time',
        'price',
        'event_img',
        'location',
        'total_seats',
        'event_type',
        'eStatus',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'event_id', 'event_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'event_id', 'event_id');
    }

    /**
     * Update event status based on start_time and duration.
     */
    public function updateStatusFromTime(): void
    {
        $now = now();
        $start = $this->start_time;
        $duration = $this->duration ?? 0;
        $end = $start->copy()->addHours($duration);

        if ($now->lt($start)) {
            $newStatus = 'Chưa diễn ra';
        } elseif ($now->gte($start) && $now->lte($end)) {
            $newStatus = 'Đang diễn ra';
        } else {
            $newStatus = 'Đã kết thúc';
        }

        if ($this->eStatus !== $newStatus) {
            $this->update(['eStatus' => $newStatus]);
        }
    }

    /**
     * Get image URL (handle full URL, storage path, or assets path).
     */
    public function getImageUrlAttribute(): string
    {
        $img = $this->event_img ?? '';
        if (str_starts_with($img, 'http')) {
            return $img;
        }
        if (str_starts_with($img, 'events/')) {
            return asset('storage/' . $img);
        }
        return asset('assets/images/' . ($img ?: 'gaudeptrai2.jpg'));
    }
}

