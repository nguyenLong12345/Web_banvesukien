<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'user_id',
        'payment_at',
        'method',
        'amount',
        'fullname',
        'email',
        'phone',
        'pStatus',
        'vnp_transaction_no',
        'payment_time',
        'meta_seats',
        'meta_event_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_at' => 'datetime',
            'payment_time' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_id', 'payment_id');
    }
}

