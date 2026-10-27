<?php

namespace App\Http\Controllers;

use App\Services\TicketmasterService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class LiveEventController extends Controller
{
    public function index(
        Request $request,
        TicketmasterService $ticketmaster
    ): View {
        $validated = $request->validate([
            'keyword' => [
                'nullable',
                'string',
                'max:100',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'country_code' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Za-z]{2}$/',
            ],
            'page' => [
                'nullable',
                'integer',
                'min:0',
                'max:49',
            ],
            'latitude' => [
                'nullable',
                'required_with:longitude',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'required_with:latitude',
                'numeric',
                'between:-180,180',
            ],
            'radius' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $keyword = trim($validated['keyword'] ?? '');
        $city = trim($validated['city'] ?? '');

        $countryCode = strtoupper(
            trim($validated['country_code'] ?? '')
        );

        if ($countryCode === 'UK') {
            $countryCode = 'GB';
        }

        $page = (int) ($validated['page'] ?? 0);

        $latitude = isset($validated['latitude'])
            ? (float) $validated['latitude']
            : null;

        $longitude = isset($validated['longitude'])
            ? (float) $validated['longitude']
            : null;

        $radius = (int) ($validated['radius'] ?? 10);

        $nearby = $latitude !== null && $longitude !== null;

        if ($nearby) {
            $city = '';
            $countryCode = '';
        }

        $events = [];

        $pagination = [
            'number' => $page,
            'size' => 20,
            'totalPages' => 0,
            'totalElements' => 0,
        ];

        $error = null;

        try {
            $result = $ticketmaster->search(
                countryCode: $countryCode,
                city: $city,
                keyword: $keyword,
                page: $page,
                latitude: $latitude,
                longitude: $longitude,
                radius: $radius
            );

            $events = $result['events'];
            $pagination = $result['page'];
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }

        return view('live-events.index', [
            'events' => $events,
            'pagination' => $pagination,
            'keyword' => $keyword,
            'city' => $city,
            'countryCode' => $countryCode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radius,
            'nearby' => $nearby,
            'error' => $error,
        ]);
    }
}