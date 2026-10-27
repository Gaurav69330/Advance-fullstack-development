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

class DiscoveryController extends Controller
{
    public function store(
        Request $request,
        TicketmasterService $ticketmaster
    ): RedirectResponse {
        $validated = $request->validate([
            'country_code' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Za-z]{2}$/',
            ],
            'city' => ['nullable', 'string', 'max:100'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $result = $ticketmaster->search(
                $validated['country_code'] ?? null,
                $validated['city'] ?? null,
                $validated['keyword'] ?? null
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        $imported = DB::transaction(function () use ($result): int {
            $count = 0;

            foreach ($result['events'] as $item) {
                $venueData = $item['_embedded']['venues'][0] ?? null;

                $eventId = $item['id'] ?? null;
                $venueId = $venueData['id'] ?? null;
                $dateTime = $item['dates']['start']['dateTime'] ?? null;

                $latitude = $venueData['location']['latitude'] ?? null;
                $longitude = $venueData['location']['longitude'] ?? null;

                // Our current schema requires a venue, coordinates,
                // and a confirmed event date.
                if (
                    !$eventId
                    || !$venueId
                    || !$dateTime
                    || !is_numeric($latitude)
                    || !is_numeric($longitude)
                    || abs((float) $latitude) > 90
                    || abs((float) $longitude) > 180
                ) {
                    continue;
                }

                try {
                    $eventDate = Carbon::parse($dateTime)
                        ->utc()
                        ->format('Y-m-d H:i:s');
                } catch (\Exception $exception) {
                    continue;
                }

                $countryCode = strtoupper(
                    (string) ($venueData['country']['countryCode'] ?? '')
                );

                if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
                    $countryCode = null;
                }

                $venue = Venue::updateOrCreate(
                    [
                        'ticketmaster_id' => $venueId,
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

                $ticketUrl = $item['url'] ?? null;

                if (
                    !is_string($ticketUrl)
                    || !filter_var($ticketUrl, FILTER_VALIDATE_URL)
                    || parse_url($ticketUrl, PHP_URL_SCHEME) !== 'https'
                ) {
                    $ticketUrl = null;
                }

                Event::updateOrCreate(
                    [
                        'ticketmaster_id' => $eventId,
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

                $count++;
            }

            return $count;
        });

        return redirect()
            ->route('events.index')
            ->with(
                'success',
                "{$imported} Ticketmaster listings imported or updated."
            );
    }
}