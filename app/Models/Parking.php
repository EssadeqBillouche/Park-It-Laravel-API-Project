<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    /** @use HasFactory<\Database\Factories\ParkingFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'total_position',
        'available_position',
        'reserved_position',
        'status',
        'region_id'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
