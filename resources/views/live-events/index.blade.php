<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Discover Live Music | GigPulse</title>

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

        header {
            padding: 20px 6%;
            border-bottom: 1px solid #d9dccf;
        }

        .header-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: #637c26;
        }

        .navigation {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        }

        .navigation form {
            margin: 0;
        }

        main {
            max-width: 1250px;
            margin: auto;
            padding: 40px 24px 60px;
        }

        h1 {
            margin-bottom: 12px;
            font-size: clamp(36px, 6vw, 60px);
            line-height: 1.1;
        }

        .intro,
        .hint {
            color: #686b60;
        }

        .hint {
            font-size: 13px;
        }

        .search-panel {
            padding: 24px;
            margin: 30px 0;
            border: 1px solid #c9d2ba;
            border-radius: 8px;
            background: #e8ecdf;
        }

        .search-fields {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr;
            align-items: start;
            gap: 16px;
        }

        .search-fields > div {
            min-width: 0;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
        }

        input:not([type="hidden"]) {
            width: 100%;
            min-height: 46px;
            margin-top: 6px;
            padding: 10px 12px;
            border: 1px solid #c8cdbd;
            border-radius: 5px;
            background: white;
            color: #20231e;
            font: inherit;
        }

        #country_code {
            text-transform: uppercase;
        }

        button,
        .button {
            display: inline-block;
            min-height: 44px;
            padding: 10px 18px;
            border: 1px solid #20231e;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #344b38;
        }

        button:disabled {
            opacity: 0.6;
            cursor: wait;
        }

        .button.secondary,
        #near-me-button {
            background: transparent;
            color: #20231e;
        }

        .button.secondary:hover,
        #near-me-button:hover {
            background: #dce4cd;
        }

        .search-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
            margin-top: 18px;
        }

        .search-suggestions a {
            overflow-wrap: anywhere;
        }

        .error,
        .success {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 5px;
        }

        .error {
            border: 1px solid #e1b1a7;
            background: #fff0ec;
            color: #8b2727;
        }

        .success {
            border: 1px solid #bbc9a4;
            background: #e8ecdf;
        }

        .results-summary {
            margin-bottom: 24px;
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
            border: 1px solid #d9dccf;
            border-radius: 8px;
            background: #fffef9;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            background: #d8f36a;
            font-size: 12px;
        }

        .card h2 {
            margin: 18px 0;
            font-size: 23px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .card p {
            margin: 6px 0;
            color: #686b60;
        }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: auto;
            padding-top: 22px;
        }

        .card-actions form {
            margin: 0;
        }

        .empty {
            padding: 40px 24px;
            border: 1px dashed #a6b18d;
            border-radius: 8px;
            text-align: center;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-top: 30px;
        }

        .pagination span {
            padding: 10px;
        }

        .disabled {
            color: #777;
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

        [hidden] {
            display: none !important;
        }

        @media (max-width: 950px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .search-fields {
                grid-template-columns: 1fr 1fr;
            }

            .keyword-field {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 600px) {
            .cards,
            .search-fields {
                grid-template-columns: 1fr;
            }

            .keyword-field {
                grid-column: auto;
            }

            .header-content {
                flex-direction: column;
                align-items: start;
            }

            main {
                padding: 25px 18px 45px;
            }

            .search-panel {
                padding: 20px;
            }

            .search-actions button {
                width: 100%;
            }

            .card-actions,
            .card-actions form,
            .card-actions .button,
            .card-actions button {
                width: 100%;
            }

            .card-actions .button {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <a class="brand" href="{{ route('events.index') }}">
                Gig<span>Pulse</span>
            </a>

            <nav class="navigation" aria-label="Main navigation">
                <a
                    href="{{ route('events.index') }}"
                    aria-current="page"
                >
                    Discover
                </a>

                <a href="{{ route('saved-events.index') }}">
                    Saved listings
                </a>

                @auth
                    <a href="{{ route('my-gigs.index') }}">My gigs</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <p class="success" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if (session('error'))
            <p class="error" role="alert">
                {{ session('error') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="error" role="alert">
                <ul>
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1>Find your next good night.</h1>

        <p class="intro">
            Discover live music worldwide, find nearby gigs,
            and save your favourites to GigPulse.
        </p>

        <form
            id="live-search-form"
            class="search-panel"
            method="GET"
            action="{{ route('events.index') }}"
            role="search"
        >
            @if ($nearby)
                <input
                    type="hidden"
                    name="latitude"
                    value="{{ $latitude }}"
                >

                <input
                    type="hidden"
                    name="longitude"
                    value="{{ $longitude }}"
                >

                <input
                    type="hidden"
                    name="radius"
                    value="{{ $radius }}"
                >
            @endif

            <div class="search-fields">
                <div class="keyword-field">
                    <label for="keyword">Artist or event</label>

                    <input
                        id="keyword"
                        name="keyword"
                        type="search"
                        value="{{ $keyword }}"
                        maxlength="100"
                        placeholder="Search an artist or event"
                        autocomplete="off"
                        aria-describedby="suggestions-hint"
                        data-suggestions-url="{{ route('search-suggestions.index') }}"
                    >

                    <p class="hint" id="suggestions-hint">
                        Suggestions show upcoming saved gigs.
                        Submit your search to find Ticketmaster listings.
                    </p>
                </div>

                <div>
                    <label for="city">City (optional)</label>

                    <input
                        id="city"
                        name="city"
                        type="text"
                        value="{{ $city }}"
                        maxlength="100"
                        placeholder="London, New York..."
                    >
                </div>

                <div>
                    <label for="country_code">
                        Country code (optional)
                    </label>

                    <input
                        id="country_code"
                        name="country_code"
                        type="text"
                        value="{{ $countryCode }}"
                        minlength="2"
                        maxlength="2"
                        pattern="[A-Za-z]{2}"
                        placeholder="GB, US, AU..."
                        aria-describedby="country-hint"
                    >
                </div>
            </div>

            <p class="hint" id="country-hint">
                Leave country and city empty for worldwide search.
                Use GB for the United Kingdom.
            </p>

            <div class="search-actions">
                <button id="search-button" type="submit">
                    Search live events →
                </button>

                <button id="near-me-button" type="button">
                    Near me
                </button>

                <a href="{{ route('events.index') }}">
                    Clear filters
                </a>
            </div>

            <p class="hint">
                Near me searches within 10 miles.
                Your browser asks permission before sharing your location.
                GigPulse sends the location to Ticketmaster to find events.
            </p>

            <p
                id="location-status"
                class="hint"
                role="status"
                aria-live="polite"
            >
                @if ($nearby)
                    Showing events within {{ $radius }} miles.
                    Type a city or country to switch to a place search,
                    or select Clear filters.
                @endif
            </p>

            <p
                id="search-status"
                class="hint"
                role="status"
                aria-live="polite"
            ></p>
        </form>

        @if ($error)
            <p class="error" role="alert">
                {{ $error }}
            </p>
        @else
            @php
                $currentPage = (int) ($pagination['number'] ?? 0);
                $totalPages = (int) ($pagination['totalPages'] ?? 0);
                $totalResults = (int) ($pagination['totalElements'] ?? 0);
                $pageSize = max(1, (int) ($pagination['size'] ?? 20));

                $accessiblePages = min(
                    $totalPages,
                    50,
                    intdiv(1000, $pageSize)
                );

                $searchFilters = array_filter([
                    'keyword' => $keyword,
                    'city' => $city,
                    'country_code' => $countryCode,
                ], fn ($value) => $value !== '');

                if ($nearby) {
                    $searchFilters['latitude'] = $latitude;
                    $searchFilters['longitude'] = $longitude;
                    $searchFilters['radius'] = $radius;
                }
            @endphp

            <div class="results-summary" role="status">
                <strong>{{ number_format($totalResults) }}</strong>
                matching listings reported by Ticketmaster.

                @if ($nearby)
                    <p class="hint">
                        Within {{ $radius }} miles of your location,
                        ordered by distance.
                    </p>
                @endif

                @if ($totalResults > 0)
                    <p class="hint">
                        Showing {{ count($events) }} listings on this page.
                    </p>
                @endif
            </div>

            @if (empty($events))
                <section class="empty">
                    <h2>No events found.</h2>

                    <p>
                        Try another artist, clear your location,
                        or search a different city or country.
                    </p>
                </section>
            @else
                <div class="cards">
                    @foreach ($events as $item)
                        @php
                            $venue = $item['_embedded']['venues'][0] ?? [];

                            $eventName = $item['name'] ?? 'Live music event';

                            $genreName =
                                $item['classifications'][0]['genre']['name']
                                ?? 'Music';

                            $localDate =
                                $item['dates']['start']['localDate']
                                ?? null;

                            $localTime =
                                $item['dates']['start']['localTime']
                                ?? null;

                            $dateUnconfirmed =
                                !empty($item['dates']['start']['dateTBA'])
                                || !empty($item['dates']['start']['dateTBD']);

                            $timeUnconfirmed =
                                !empty($item['dates']['start']['timeTBA']);

                            $ticketmasterId = $item['id'] ?? '';

                            $validEventId =
                                is_string($ticketmasterId)
                                && preg_match(
                                    '/^[A-Za-z0-9_-]{1,100}$/',
                                    $ticketmasterId
                                );

                            $ticketUrl = $item['url'] ?? null;

                            $validTicketUrl =
                                is_string($ticketUrl)
                                && filter_var(
                                    $ticketUrl,
                                    FILTER_VALIDATE_URL
                                )
                                && parse_url(
                                    $ticketUrl,
                                    PHP_URL_SCHEME
                                ) === 'https';

                            $distance = $item['distance'] ?? null;
                        @endphp

                        <article class="card">
                            <span class="badge">{{ $genreName }}</span>

                            <h2>{{ $eventName }}</h2>

                            <p>
                                <strong>Date:</strong>

                                @if ($localDate && !$dateUnconfirmed)
                                    <time datetime="{{ $localDate }}">
                                        {{ $localDate }}
                                    </time>
                                @else
                                    To be confirmed
                                @endif
                            </p>

                            <p>
                                <strong>Time:</strong>

                                @if ($localTime && !$timeUnconfirmed)
                                    {{ substr($localTime, 0, 5) }}
                                    (venue local time)
                                @else
                                    To be confirmed
                                @endif
                            </p>

                            <p>
                                <strong>Venue:</strong>
                                {{ $venue['name'] ?? 'To be confirmed' }}
                            </p>

                            <p>
                                <strong>City:</strong>
                                {{ $venue['city']['name'] ?? 'To be confirmed' }}
                            </p>

                            <p>
                                <strong>Country:</strong>
                                {{ $venue['country']['name'] ?? 'To be confirmed' }}
                            </p>

                            @if ($nearby && is_numeric($distance))
                                <p>
                                    <strong>Distance:</strong>
                                    {{ number_format((float) $distance, 1) }}
                                    {{ $item['units'] ?? 'miles' }}
                                </p>
                            @endif

                            <p>
                                <strong>Status:</strong>
                                {{ ucfirst($item['dates']['status']['code'] ?? 'Unknown') }}
                            </p>

                            <div class="card-actions">
                                @if ($validEventId)
                                    @auth
                                        <form
                                            class="save-event-form"
                                            method="POST"
                                            action="{{ route('live-events.save') }}"
                                        >
                                            @csrf

                                            <input
                                                type="hidden"
                                                name="ticketmaster_id"
                                                value="{{ $ticketmasterId }}"
                                            >

                                            <button type="submit">
                                                Save to GigPulse →
                                            </button>
                                        </form>
                                    @else
                                        <a
                                            class="button"
                                            href="{{ route('login') }}"
                                        >
                                            Sign in to save
                                        </a>
                                    @endauth
                                @endif

                                @if ($validTicketUrl)
                                    <a
                                        class="button secondary"
                                        href="{{ $ticketUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Tickets ↗
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($accessiblePages > 1)
                <nav class="pagination" aria-label="Live event pages">
                    @if ($currentPage > 0)
                        <a
                            class="button"
                            href="{{ route('events.index', array_merge($searchFilters, ['page' => $currentPage - 1])) }}"
                            rel="prev"
                        >
                            ← Previous
                        </a>
                    @else
                        <span class="disabled" aria-disabled="true">
                            ← Previous
                        </span>
                    @endif

                    <span>
                        Page {{ $currentPage + 1 }}
                        of {{ $accessiblePages }} accessible pages
                    </span>

                    @if ($currentPage + 1 < $accessiblePages)
                        <a
                            class="button"
                            href="{{ route('events.index', array_merge($searchFilters, ['page' => $currentPage + 1])) }}"
                            rel="next"
                        >
                            Next →
                        </a>
                    @else
                        <span class="disabled" aria-disabled="true">
                            Next →
                        </span>
                    @endif
                </nav>
            @endif

            @if ($totalPages > $accessiblePages)
                <p class="hint">
                    Add an artist, city, or country to narrow this search
                    beyond the accessible result pages.
                </p>
            @endif
        @endif
    </main>

    <footer>
        GigPulse — Live listings supplied by Ticketmaster.
        Coverage varies by country and event.
    </footer>

    <script>
        (() => {
            const searchForm =
                document.getElementById('live-search-form');

            if (searchForm) {
                searchForm.addEventListener('submit', () => {
                    const button =
                        document.getElementById('search-button');

                    const status =
                        document.getElementById('search-status');

                    button.disabled = true;
                    button.textContent = 'Searching…';

                    status.textContent =
                        'Searching Ticketmaster. Please wait.';
                });

                function clearNearbyLocation() {
                    ['latitude', 'longitude', 'radius'].forEach((name) => {
                        searchForm.querySelector(
                            `input[name="${name}"]`
                        )?.remove();
                    });

                    document.getElementById('location-status')
                        .textContent =
                            'Place search selected. Click Near me to use your location.';
                }

                document.getElementById('city')
                    .addEventListener('input', clearNearbyLocation);

                document.getElementById('country_code')
                    .addEventListener('input', clearNearbyLocation);
            }

            document.querySelectorAll('.save-event-form')
                .forEach((form) => {
                    form.addEventListener('submit', () => {
                        const button = form.querySelector('button');

                        button.disabled = true;
                        button.textContent = 'Saving…';
                    });
                });

            window.addEventListener('pageshow', () => {
                const searchButton =
                    document.getElementById('search-button');

                if (searchButton) {
                    searchButton.disabled = false;
                    searchButton.textContent = 'Search live events →';
                }

                const nearMeButton =
                    document.getElementById('near-me-button');

                if (nearMeButton) {
                    nearMeButton.disabled = false;
                    nearMeButton.textContent = 'Near me';
                }

                document.querySelectorAll('.save-event-form button')
                    .forEach((button) => {
                        button.disabled = false;
                        button.textContent = 'Save to GigPulse →';
                    });

                document.getElementById('search-status').textContent = '';
            });
        })();
    </script>

    <script src="{{ asset('js/near-me.js') }}" defer></script>
    <script src="{{ asset('js/search-preferences.js') }}" defer></script>
    <script src="{{ asset('js/search-suggestions.js') }}" defer></script>
</body>
</html>