<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TicketmasterService
{
    public function search(
        ?string $countryCode = null,
        ?string $city = null,
        ?string $keyword = null,
        int $page = 0,
        ?float $latitude = null,
        ?float $longitude = null,
        int $radius = 10
    ): array {
        $apiKey = $this->apiKey();

        $countryCode = strtoupper(trim($countryCode ?? ''));
        $city = trim($city ?? '');
        $keyword = trim($keyword ?? '');

        if ($countryCode === 'UK') {
            $countryCode = 'GB';
        }

        if (
            $countryCode !== ''
            && !preg_match('/^[A-Z]{2}$/', $countryCode)
        ) {
            throw new RuntimeException(
                'Enter a two-letter country code, such as GB or US.'
            );
        }

        if (
            mb_strlen($city) > 100
            || mb_strlen($keyword) > 100
        ) {
            throw new RuntimeException(
                'City and artist searches must be 100 characters or fewer.'
            );
        }

        if ($page < 0 || $page > 49) {
            throw new RuntimeException(
                'Choose a page between 0 and 49.'
            );
        }

        $hasLocation = $latitude !== null || $longitude !== null;

        if ($hasLocation) {
            if ($latitude === null || $longitude === null) {
                throw new RuntimeException(
                    'Both latitude and longitude are required.'
                );
            }

            if (
                !is_finite($latitude)
                || !is_finite($longitude)
                || abs($latitude) > 90
                || abs($longitude) > 180
            ) {
                throw new RuntimeException(
                    'The supplied location is invalid.'
                );
            }

            if ($radius < 1 || $radius > 100) {
                throw new RuntimeException(
                    'Choose a distance between 1 and 100 miles.'
                );
            }
        }

        $parameters = [
            'classificationName' => 'Music',
            'size' => 20,
            'page' => $page,
            'sort' => 'date,asc',
        ];

        if ($keyword !== '') {
            $parameters['keyword'] = $keyword;
        }

        if ($hasLocation) {
            // Ticketmaster accepts a geohash for location searches.
            $parameters['geoPoint'] = $this->geohash(
                $latitude,
                $longitude
            );

            $parameters['radius'] = $radius;
            $parameters['unit'] = 'miles';
            $parameters['sort'] = 'distance,asc';
        } else {
            if ($countryCode !== '') {
                $parameters['countryCode'] = $countryCode;
            }

            if ($city !== '') {
                $parameters['city'] = $city;
            }
        }

        $cacheKey = 'ticketmaster.search.' . hash(
            'sha256',
            json_encode($parameters) . hash('sha256', $apiKey)
        );

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($parameters, $apiKey): array {
                $data = $this->fetch(
                    'events.json',
                    array_merge($parameters, [
                        'apikey' => $apiKey,
                    ])
                );

                return [
                    'events' => $data['_embedded']['events'] ?? [],
                    'page' => $data['page'] ?? [
                        'number' => $parameters['page'],
                        'size' => $parameters['size'],
                        'totalPages' => 0,
                        'totalElements' => 0,
                    ],
                ];
            }
        );
    }

    public function findEvent(string $ticketmasterId): array
    {
        $apiKey = $this->apiKey();

        if (
            !preg_match(
                '/^[A-Za-z0-9_-]{1,100}$/',
                $ticketmasterId
            )
        ) {
            throw new RuntimeException(
                'Invalid Ticketmaster event ID.'
            );
        }

        $cacheKey = 'ticketmaster.event.' . hash(
            'sha256',
            $ticketmasterId . hash('sha256', $apiKey)
        );

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($ticketmasterId, $apiKey): array {
                $event = $this->fetch(
                    'events/'
                    . rawurlencode($ticketmasterId)
                    . '.json',
                    [
                        'apikey' => $apiKey,
                    ]
                );

                if (
                    ($event['id'] ?? null) !== $ticketmasterId
                ) {
                    throw new RuntimeException(
                        'Ticketmaster returned an unexpected event.'
                    );
                }

                return $event;
            }
        );
    }

    private function apiKey(): string
    {
        $apiKey = trim(
            (string) config('services.ticketmaster.key', '')
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'Ticketmaster API key is missing. '
                . 'Check .env and config/services.php, '
                . 'then run php artisan config:clear.'
            );
        }

        return $apiKey;
    }

    private function fetch(
        string $endpoint,
        array $parameters
    ): array {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->get(
                    'https://app.ticketmaster.com/discovery/v2/'
                    . $endpoint,
                    $parameters
                );
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Could not connect to Ticketmaster. '
                . 'Please check your connection and try again.'
            );
        }

        if ($response->status() === 404) {
            throw new RuntimeException(
                'The requested Ticketmaster listing was not found.'
            );
        }

        if (in_array($response->status(), [401, 403], true)) {
            throw new RuntimeException(
                'Ticketmaster rejected the API key. '
                . 'Check your Consumer Key.'
            );
        }

        if ($response->status() === 429) {
            throw new RuntimeException(
                'Ticketmaster’s request limit has been reached. '
                . 'Please try again later.'
            );
        }

        if (!$response->successful()) {
            throw new RuntimeException(
                'Ticketmaster is temporarily unavailable. '
                . 'Please try again later.'
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Ticketmaster returned an unexpected response.'
            );
        }

        return $data;
    }

    private function geohash(
        float $latitude,
        float $longitude,
        int $precision = 8
    ): string {
        $alphabet = '0123456789bcdefghjkmnpqrstuvwxyz';

        $latitudeRange = [-90.0, 90.0];
        $longitudeRange = [-180.0, 180.0];

        $hash = '';
        $character = 0;
        $bitCount = 0;
        $useLongitude = true;

        while (strlen($hash) < $precision) {
            if ($useLongitude) {
                $middle = (
                    $longitudeRange[0] + $longitudeRange[1]
                ) / 2;

                $upperHalf = $longitude >= $middle;

                if ($upperHalf) {
                    $longitudeRange[0] = $middle;
                } else {
                    $longitudeRange[1] = $middle;
                }
            } else {
                $middle = (
                    $latitudeRange[0] + $latitudeRange[1]
                ) / 2;

                $upperHalf = $latitude >= $middle;

                if ($upperHalf) {
                    $latitudeRange[0] = $middle;
                } else {
                    $latitudeRange[1] = $middle;
                }
            }

            $character = ($character << 1)
                | ($upperHalf ? 1 : 0);

            $bitCount++;
            $useLongitude = !$useLongitude;

            if ($bitCount === 5) {
                $hash .= $alphabet[$character];

                $character = 0;
                $bitCount = 0;
            }
        }

        return $hash;
    }
}