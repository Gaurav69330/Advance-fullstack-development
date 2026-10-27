<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'venue_id',
        'artist_name',
        'event_date',
        'genre',
        'spotify_artist_id',
        'description',
        'ticketmaster_id',
        'ticket_url',
        'is_demo',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(
            Venue::class,
            'venue_id',
            'venue_id'
        );
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            Attendance::class,
            'event_id',
            'event_id'
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'event_id',
            'event_id'
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(
            Comment::class,
            'event_id',
            'event_id'
        );
    }
}