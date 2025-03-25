<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'hourly_rate',
        'Fee',
        'status',
        'parking_id'
    ];

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
