<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $table = 'venues';

    protected $primaryKey = 'venue_id';

    protected $fillable = [
        'name',
        'city',
        'country_code',
        'latitude',
        'longitude',
        'ticketmaster_id',
        'timezone',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(
            Event::class,
            'venue_id',
            'venue_id'
        );
    }
}