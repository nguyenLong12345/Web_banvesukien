<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $table = 'seats';

    protected $primaryKey = 'seat_id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'seat_id',
        'event_id',
        'seat_type',
        'seat_number',
        'sStatus',
        'seat_price',
    ];

    protected function casts(): array
    {
        return [
            'seat_price' => 'float',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'seat_id', 'seat_id');
    }

    public function isBooked(): bool
    {
        return $this->sStatus === 'Đã đặt';
    }
}

