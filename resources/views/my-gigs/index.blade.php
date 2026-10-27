<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Gigs | GigPulse</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f6f4ec;
            color: #20231e;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        a {
            color: inherit;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 45px 24px;
        }

        h1 {
            margin-bottom: 10px;
            font-size: clamp(36px, 6vw, 60px);
            line-height: 1.1;
        }

        .intro,
        .hint {
            color: #686b60;
        }

        .intro {
            margin-bottom: 30px;
        }

        .hint {
            font-size: 13px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: start;
            padding: 24px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 8px;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            background: #d8f36a;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .card h2 {
            margin: 18px 0 10px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .card p {
            margin: 6px 0;
        }

        .button {
            display: inline-block;
            min-height: 44px;
            padding: 11px 20px;
            border: 1px solid #20231e;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            text-decoration: none;
        }

        .button:hover {
            background: #344b38;
        }

        .card .button {
            margin-top: auto;
        }

        .card-details {
            margin-bottom: 22px;
        }

        .empty {
            padding: 40px 24px;
            border: 1px dashed #a6b18d;
            border-radius: 8px;
            text-align: center;
        }

        .success-message {
            padding: 14px 18px;
            background: #e8ecdf;
            border: 1px solid #bbc9a4;
            border-radius: 5px;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 30px;
        }

        footer {
            padding: 24px;
            border-top: 1px solid #d9dccf;
            color: #686b60;
            text-align: center;
        }

        :focus-visible {
            outline: 3px solid #637c26;
            outline-offset: 4px;
        }

        @media (max-width: 900px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .cards {
                grid-template-columns: 1fr;
            }

            main {
                padding: 30px 18px;
            }
        }
    </style>
</head>

<body>
    @include('partials.header')

    @php
        $validTimezones = \DateTimeZone::listIdentifiers(
            \DateTimeZone::ALL_WITH_BC
        );
    @endphp

    <main>
        @if (session('success'))
            <p class="success-message" role="status">
                {{ session('success') }}
            </p>
        @endif

        <h1>My gigs.</h1>

        <p class="intro">
            Your upcoming plans and the nights you remember.
        </p>

        <p>
            {{ $attendances->total() }}
            {{ $attendances->total() === 1 ? 'saved gig' : 'saved gigs' }}
        </p>

        @if ($attendances->isEmpty())
            <section class="empty">
                <h2>Your next good night is waiting.</h2>

                <p>
                    Find an event on Discover and select Save to GigPulse.
                    It will appear here.
                </p>

                <a class="button" href="{{ route('events.index') }}">
                    Discover gigs →
                </a>
            </section>
        @else
            <div class="cards">
                @foreach ($attendances as $attendance)
                    @php
                        $gig = $attendance->event;

                        $venueTimezone = $gig?->venue?->timezone;

                        $hasVenueTimezone = is_string($venueTimezone)
                            && in_array(
                                $venueTimezone,
                                $validTimezones,
                                true
                            );

                        $displayTimezone = $hasVenueTimezone
                            ? $venueTimezone
                            : 'UTC';

                        $localEventDate = $gig?->event_date
                            ? $gig->event_date
                                ->copy()
                                ->setTimezone($displayTimezone)
                            : null;
                    @endphp

                    @if ($gig && $localEventDate)
                        <article class="card">
                            <span class="status">
                                {{ $attendance->status }}
                            </span>

                            <h2>{{ $gig->artist_name }}</h2>

                            <div class="card-details">
                                @if ($gig->is_demo)
                                    <p class="hint">
                                        Fictional sample gig
                                    </p>
                                @endif

                                <p>
                                    <strong>Date:</strong>

                                    <time datetime="{{ $localEventDate->toIso8601String() }}">
                                        {{ $localEventDate->format('D, j M Y') }}
                                    </time>
                                </p>

                                <p>
                                    <strong>
                                        {{ $hasVenueTimezone
                                            ? 'Venue local time:'
                                            : 'Time (UTC):' }}
                                    </strong>

                                    {{ $localEventDate->format('H:i') }}
                                </p>

                                <p class="hint">
                                    {{ $displayTimezone }}
                                </p>

                                <p>
                                    <strong>Venue:</strong>
                                    {{ $gig->venue?->name ?? 'To be confirmed' }}
                                </p>

                                <p>
                                    <strong>City:</strong>
                                    {{ $gig->venue?->city ?? 'To be confirmed' }}

                                    @if ($gig->venue?->country_code)
                                        · {{ $gig->venue->country_code }}
                                    @endif
                                </p>

                                @if ($gig->event_date->isPast())
                                    <p class="hint">
                                        This event has taken place.
                                        Reviews are open.
                                    </p>
                                @else
                                    <p class="hint">
                                        An upcoming night out.
                                    </p>
                                @endif

                                @if ($attendance->ticket_photo_path)
                                    <p class="hint">
                                        You have saved a photo for this gig.
                                    </p>
                                @endif
                            </div>

                            <a
                                class="button"
                                href="{{ route('events.show', $gig) }}"
                                aria-label="View gig: {{ $gig->artist_name }}"
                            >
                                View gig →
                            </a>
                        </article>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($attendances->hasPages())
            <nav class="pagination" aria-label="Saved gig pages">
                @if ($attendances->onFirstPage())
                    <span aria-disabled="true">← Previous</span>
                @else
                    <a
                        class="button"
                        href="{{ $attendances->previousPageUrl() }}"
                        rel="prev"
                    >
                        ← Previous
                    </a>
                @endif

                <span>
                    Page {{ $attendances->currentPage() }}
                    of {{ $attendances->lastPage() }}
                </span>

                @if ($attendances->hasMorePages())
                    <a
                        class="button"
                        href="{{ $attendances->nextPageUrl() }}"
                        rel="next"
                    >
                        Next →
                    </a>
                @else
                    <span aria-disabled="true">Next →</span>
                @endif
            </nav>
        @endif
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>
</body>
</html>