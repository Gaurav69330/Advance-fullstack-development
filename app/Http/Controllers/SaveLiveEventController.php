<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Services\TicketmasterService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class SaveLiveEventController extends Controller
{
    public function store(
        Request $request,
        TicketmasterService $ticketmaster
    ): RedirectResponse {
        $validated = $request->validate([
            'ticketmaster_id' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
            ],
        ]);

        try {
            $item = $ticketmaster->findEvent(
                $validated['ticketmaster_id']
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $venueData = $item['_embedded']['venues'][0] ?? null;

        $dateTime = $item['dates']['start']['dateTime'] ?? null;
        $latitude = $venueData['location']['latitude'] ?? null;
        $longitude = $venueData['location']['longitude'] ?? null;

        if (
            !$venueData
            || empty($venueData['id'])
            || !$dateTime
            || !is_numeric($latitude)
            || !is_numeric($longitude)
            || abs((float) $latitude) > 90
            || abs((float) $longitude) > 180
        ) {
            return back()->with(
                'error',
                'This event cannot be saved yet because its confirmed '
                . 'date or venue location is unavailable.'
            );
        }

        try {
            $eventDate = Carbon::parse($dateTime)
                ->utc()
                ->format('Y-m-d H:i:s');
        } catch (\Exception $exception) {
            return back()->with(
                'error',
                'This event has an invalid date.'
            );
        }

        $countryCode = strtoupper(
            (string) ($venueData['country']['countryCode'] ?? '')
        );

        if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
            $countryCode = null;
        }

        $ticketUrl = $item['url'] ?? null;

        if (
            !is_string($ticketUrl)
            || !filter_var($ticketUrl, FILTER_VALIDATE_URL)
            || parse_url($ticketUrl, PHP_URL_SCHEME) !== 'https'
        ) {
            $ticketUrl = null;
        }

        $event = DB::transaction(function () use (
            $item,
            $venueData,
            $eventDate,
            $latitude,
            $longitude,
            $countryCode,
            $ticketUrl,
            $request
        ): Event {
            $venue = Venue::updateOrCreate(
                [
                    'ticketmaster_id' => $venueData['id'],
                ],
                [
                    'name' => Str::limit(
                        $venueData['name'] ?? 'Unknown venue',
                        255,
                        ''
                    ),
                    'city' => Str::limit(
                        $venueData['city']['name'] ?? 'Unknown city',
                        255,
                        ''
                    ),
                    'country_code' => $countryCode,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]
            );

            $event = Event::updateOrCreate(
                [
                    'ticketmaster_id' => $item['id'],
                ],
                [
                    'venue_id' => $venue->venue_id,
                    'artist_name' => Str::limit(
                        $item['name'] ?? 'Live music event',
                        255,
                        ''
                    ),
                    'event_date' => $eventDate,
                    'genre' => Str::limit(
                        $item['classifications'][0]['genre']['name']
                            ?? 'Music',
                        255,
                        ''
                    ),
                    'description' => $item['info']
                        ?? 'Live event listing supplied by Ticketmaster.',
                    'ticket_url' => $ticketUrl,
                    'is_demo' => false,
                ]
            );

            // Save to this user's diary without overwriting an
            // existing Attending or Went status.
            $event->attendances()->firstOrCreate(
                [
                    'user_id' => $request->user()->id,
                ],
                [
                    'status' => 'Interested',
                ]
            );

            return $event;
        });

        return redirect()
            ->route('events.show', $event)
            ->with(
                'success',
                'Event saved to GigPulse and your My gigs list.'
            );
    }
}