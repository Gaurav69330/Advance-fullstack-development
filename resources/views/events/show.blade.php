<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>{{ $event->artist_name }} | GigPulse</title>

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
            color: #20231e;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: #637c26;
        }

        .account-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            font-size: 14px;
        }

        .account-nav a {
            color: #20231e;
        }

        .account-nav form {
            margin: 0;
        }

        main {
            max-width: 1000px;
            margin: auto;
            padding: 40px 24px 70px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: #20231e;
        }

        .event-card {
            padding: 40px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 10px;
        }

        .genre {
            display: inline-block;
            padding: 5px 14px;
            background: #d8f36a;
            border-radius: 20px;
            font-size: 13px;
        }

        h1 {
            margin: 20px 0;
            font-size: clamp(36px, 6vw, 64px);
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        h2 {
            margin-top: 0;
        }

        .description {
            color: #686b60;
            font-size: 17px;
            white-space: pre-line;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid #d9dccf;
        }

        .details dt {
            margin-bottom: 6px;
            color: #686b60;
            font-size: 13px;
        }

        .details dd {
            margin: 0;
            font-size: 17px;
            font-weight: bold;
        }

        .notice {
            margin-top: 25px;
            padding: 15px;
            background: #e8ecdf;
            border-radius: 5px;
        }

        .attendance-panel {
            margin-top: 30px;
            padding: 25px;
            background: #e8ecdf;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select {
            display: block;
            width: 100%;
            max-width: 350px;
            min-height: 46px;
            padding: 10px;
            border: 1px solid #bbc5a8;
            border-radius: 5px;
            background: white;
            font: inherit;
        }

        button,
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

        #attendance-form button {
            margin-top: 15px;
        }

        .feedback {
            padding: 12px 16px;
            border-radius: 5px;
            background: #e8ecdf;
            border: 1px solid #bbc9a4;
        }

        .feedback.error {
            background: #fff0ec;
            border-color: #e1b1a7;
            color: #8b2727;
        }

        #attendance-feedback {
            margin-bottom: 0;
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
            display: none;
        }

        @media (max-width: 600px) {
            main {
                padding: 25px 16px 45px;
            }

            .event-card {
                padding: 24px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                align-items: start;
            }

            .attendance-panel {
                padding: 20px;
            }

            select,
            #attendance-form button {
                max-width: none;
                width: 100%;
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

            <nav class="account-nav" aria-label="Main navigation">
                <a href="{{ route('events.index') }}">Discover</a>

                @auth
                    <span>Hello, {{ auth()->user()->name }}</span>

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
        <a class="back-link" href="{{ route('events.index') }}">
            ← Back to discovery
        </a>

        @if (session('success'))
            <p class="feedback" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="feedback error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <article class="event-card">
            <span class="genre">
                {{ $event->genre ?: 'Live music' }}
            </span>

            <h1>{{ $event->artist_name }}</h1>

            <p class="description">{{ $event->description ?: 'More details about this gig will be available soon.' }}</p>

            <dl class="details">
                <div>
                    <dt>Date</dt>
                    <dd>
                        {{ $event->event_date->format('l, j F Y') }}
                    </dd>
                </div>

                <div>
                    <dt>Time</dt>
                    <dd>
                        {{ $event->event_date->format('H:i') }}
                    </dd>
                </div>

                <div>
                    <dt>Venue</dt>
                    <dd>
                        {{ $event->venue?->name ?? 'To be confirmed' }}
                    </dd>
                </div>

                <div>
                    <dt>City</dt>
                    <dd>
                        {{ $event->venue?->city ?? 'To be confirmed' }}
                    </dd>
                </div>
            </dl>

            @if ($event->event_date->isPast())
                <p class="notice">
                    This event has already taken place.
                </p>
            @else
                <p class="notice">
                    Something to look forward to. Plan your next night out!
                </p>
            @endif

            <section class="attendance-panel">
                <h2>Make it a plan.</h2>

                <p>
                    <strong id="attendance-count">
                        {{ $event->attendances()->where('status', 'Attending')->count() }}
                    </strong>
                    going
                </p>

                @auth
                    @php
                        $savedStatus = $event->attendances()
                            ->where('user_id', auth()->id())
                            ->value('status');

                        $selectedStatus = old(
                            'status',
                            $savedStatus ?? 'Interested'
                        );
                    @endphp

                    <p id="saved-status">
                        {{ $savedStatus ? 'Your status: ' . $savedStatus : 'You have not saved this gig yet.' }}
                    </p>

                    <form
                        id="attendance-form"
                        method="POST"
                        action="{{ route('attendance.store', $event) }}"
                    >
                        @csrf

                        <label for="status">Your plans</label>

                        <select id="status" name="status" required>
                            <option
                                value="Interested"
                                @selected($selectedStatus === 'Interested')
                            >
                                Interested
                            </option>

                            <option
                                value="Attending"
                                @selected($selectedStatus === 'Attending')
                            >
                                Attending
                            </option>

                            @if ($event->event_date->isPast())
                                <option
                                    value="Went"
                                    @selected($selectedStatus === 'Went')
                                >
                                    Went
                                </option>
                            @endif
                        </select>

                        <button id="save-attendance" type="submit">
                            Save my plans
                        </button>
                    </form>

                    <p
                        id="attendance-feedback"
                        role="status"
                        aria-live="polite"
                        hidden
                    ></p>
                @else
                    <p>Sign in to save this gig and track your plans.</p>

                    <a class="button" href="{{ route('login') }}">
                        Sign in to save
                    </a>
                @endauth
            </section>
        </article>
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>

    <script>
        const attendanceForm = document.getElementById('attendance-form');

        if (attendanceForm) {
            attendanceForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = document.getElementById('save-attendance');
                const feedback = document.getElementById('attendance-feedback');

                button.disabled = true;
                button.textContent = 'Saving…';
                feedback.hidden = true;

                try {
                    const response = await fetch(attendanceForm.action, {
                        method: 'POST',
                        body: new FormData(attendanceForm),
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const contentType = response.headers.get('content-type') || '';

                    if (!contentType.includes('application/json')) {
                        throw new Error(
                            'Your session may have expired. Refresh the page and try again.'
                        );
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        let message = data.message || 'Could not save your plans.';

                        if (data.errors) {
                            message = Object.values(data.errors)
                                .flat()
                                .join(' ');
                        }

                        throw new Error(message);
                    }

                    document.getElementById('attendance-count').textContent =
                        data.attendance_count;

                    document.getElementById('saved-status').textContent =
                        'Your status: ' + data.status;

                    feedback.textContent = data.message;
                    feedback.className = 'feedback';
                    feedback.hidden = false;
                } catch (error) {
                    feedback.textContent = error.message;
                    feedback.className = 'feedback error';
                    feedback.hidden = false;
                } finally {
                    button.disabled = false;
                    button.textContent = 'Save my plans';
                }
            });
        }
    </script>
</body>
</html>