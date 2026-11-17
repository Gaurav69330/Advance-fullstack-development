@php
    $name = $item['name'] ?? 'Live music event';
    $venue = $item['_embedded']['venues'][0] ?? [];
    $start = $item['dates']['start'] ?? [];
    $date = empty($start['dateTBA']) && empty($start['dateTBD'])
        ? ($start['localDate'] ?? null)
        : null;
    $time = $date && empty($start['timeTBA'])
        ? ($start['localTime'] ?? null)
        : null;
    $ticketUrl = $item['url'] ?? null;

    $images = collect($item['images'] ?? [])->filter(
        fn ($image) => is_string($image['url'] ?? null)
            && str_starts_with($image['url'], 'https://')
    );

    $cover = $images->first(
        fn ($image) => ($image['ratio'] ?? '') === '16_9'
            && empty($image['fallback'])
    ) ?? $images->first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }} | GigPulse</title>
    <style>
        body {
            margin: 0;
            background: #f4f2e9;
            color: #20231e;
            font: 1rem/1.55 system-ui, sans-serif;
        }

        main {
            max-width: 52rem;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        article {
            margin-top: 1rem;
            padding: clamp(1rem, 4vw, 2rem);
            border: 1px solid #d6d8ca;
            border-radius: 12px;
            background: #fffdf7;
        }

        h1 {
            overflow-wrap: anywhere;
        }

        a {
            color: inherit;
        }

        dt {
            font-weight: 700;
        }

        dd {
            margin: 0 0 0.75rem;
        }

        .cover {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: 8px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .button {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0 1rem;
            border: 0;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main>
        <a href="{{ route('events.index') }}">← Back to Discover</a>

        <article>
            @if ($cover)
                <img class="cover" src="{{ $cover['url'] }}" alt="">
            @endif

            <h1>{{ $name }}</h1>

            @if (session('error'))
                <p role="alert">{{ session('error') }}</p>
            @endif

            <dl>
                <dt>Date</dt>
                <dd>{{ $date ?? 'To be confirmed' }}</dd>

                <dt>Time at the venue</dt>
                <dd>{{ $time ? substr($time, 0, 5) : 'To be confirmed' }}</dd>

                <dt>Venue</dt>
                <dd>{{ $venue['name'] ?? 'To be confirmed' }}</dd>

                <dt>City</dt>
                <dd>{{ $venue['city']['name'] ?? 'To be confirmed' }}</dd>

                @if (!empty($item['dates']['status']['code']))
                    <dt>Status</dt>
                    <dd>{{ ucfirst($item['dates']['status']['code']) }}</dd>
                @endif
            </dl>

            @if (!empty($item['info']))
                <p>{{ $item['info'] }}</p>
            @endif

            <div class="actions">
                @auth
                    <form method="POST" action="{{ route('live-events.save') }}">
                        @csrf
                        <input type="hidden" name="ticketmaster_id" value="{{ $item['id'] }}">
                        <button class="button" type="submit">Save to GigPulse</button>
                    </form>
                @else
                    <a class="button" href="{{ route('login') }}">Sign in to save</a>
                @endauth

                @if (is_string($ticketUrl) && str_starts_with($ticketUrl, 'https://'))
                    <a href="{{ $ticketUrl }}">Tickets on Ticketmaster ↗</a>
                @endif
            </div>
        </article>
    </main>
</body>
</html>