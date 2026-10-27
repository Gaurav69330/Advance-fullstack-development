<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        a {
            color: inherit;
        }

        header {
            padding: 20px 6%;
            border-bottom: 1px solid #d9dccf;
        }

        .header-content,
        .navigation,
        .badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
        }

        .header-content {
            justify-content: space-between;
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
            font-size: 14px;
        }

        .navigation form {
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
        }

        .event-card {
            padding: 40px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 10px;
        }

        .badges {
            gap: 8px;
        }

        .badge {
            display: inline-block;
            padding: 5px 14px;
            background: #d8f36a;
            border-radius: 20px;
            font-size: 13px;
        }

        .badge.secondary {
            background: #e8ecdf;
        }

        h1 {
            margin: 20px 0;
            font-size: clamp(36px, 6vw, 64px);
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        h2 {
            margin-top: 0;
            line-height: 1.2;
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

        .notice,
        .panel {
            padding: 24px;
            margin-top: 28px;
            background: #e8ecdf;
            border-radius: 8px;
        }

        .hint {
            color: #686b60;
            font-size: 13px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select,
        input[type="file"] {
            display: block;
            width: 100%;
            max-width: 450px;
            min-height: 46px;
            padding: 10px;
            border: 1px solid #bbc5a8;
            border-radius: 5px;
            background: white;
            color: #20231e;
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

        .panel form button {
            margin-top: 15px;
        }

        .feedback {
            padding: 12px 16px;
            border: 1px solid #bbc9a4;
            border-radius: 5px;
            background: #e8ecdf;
        }

        .feedback.error {
            border-color: #e1b1a7;
            background: #fff0ec;
            color: #8b2727;
        }

        .photo-choice {
            margin: 20px 0;
        }

        .photo-preview,
        .saved-photo {
            display: block;
            max-width: 100%;
            max-height: 420px;
            margin-top: 20px;
            border: 1px solid #c8cdbd;
            border-radius: 8px;
            object-fit: contain;
        }

        .saved-photo-link {
            display: inline-block;
            margin-top: 10px;
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

            .panel {
                padding: 20px;
            }

            select,
            input[type="file"],
            .panel form button {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>

<body>
    @php
        $attendance = auth()->check()
            ? $event->attendances()
                ->where('user_id', auth()->id())
                ->first()
            : null;

        $savedStatus = $attendance?->status;

        $selectedStatus = old(
            'status',
            $savedStatus ?? 'Interested'
        );

        $attendanceCount = $event->attendances()
            ->where('status', 'Attending')
            ->count();
    @endphp

    <header>
        <div class="header-content">
            <a class="brand" href="{{ route('events.index') }}">
                Gig<span>Pulse</span>
            </a>

            <nav class="navigation" aria-label="Main navigation">
                <a href="{{ route('events.index') }}">Discover</a>

                <a href="{{ route('saved-events.index') }}">
                    Saved listings
                </a>

                @auth
                    <a href="{{ route('my-gigs.index') }}">My gigs</a>

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
            ← Back to Discover
        </a>

        @if (session('success'))
            <p class="feedback" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if (session('error'))
            <p class="feedback error" role="alert">
                {{ session('error') }}
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
            <div class="badges">
                <span class="badge">
                    {{ $event->genre ?: 'Live music' }}
                </span>

                @if ($event->is_demo)
                    <span class="badge secondary">
                        Fictional sample gig
                    </span>
                @endif
            </div>

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
                    <dt>Time ({{ config('app.timezone', 'UTC') }})</dt>
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
                    <dt>Location</dt>
                    <dd>
                        {{ $event->venue?->city ?? 'To be confirmed' }}

                        @if ($event->venue?->country_code)
                            · {{ $event->venue->country_code }}
                        @endif
                    </dd>
                </div>
            </dl>

            @if ($event->event_date->isPast())
                <p class="notice">
                    This event has already taken place.
                </p>
            @endif

            <section class="panel" aria-labelledby="plans-heading">
                <h2 id="plans-heading">Make it a plan.</h2>

                <p>
                    <strong id="attendance-count">
                        {{ $attendanceCount }}
                    </strong>
                    going
                </p>

                @auth
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

            @auth
                <section class="panel" aria-labelledby="photo-heading">
                    <h2 id="photo-heading">Keep the memory.</h2>

                    <p>
                        Add a ticket stub or concert photo.
                        Your photo is private to your account.
                    </p>

                    <p class="hint">
                        JPEG, PNG, or WebP. Maximum file size: 5 MB.
                        Saving another photo replaces the previous one.
                    </p>

                    @if ($attendance?->ticket_photo_path)
                        <h3>Your saved photo</h3>

                        <img
                            class="saved-photo"
                            src="{{ route('ticket-photos.show', $attendance) }}"
                            alt="Your saved ticket or concert photo"
                            loading="lazy"
                        >

                        <a
                            class="saved-photo-link"
                            href="{{ route('ticket-photos.show', $attendance) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            Open full photo ↗
                        </a>
                    @endif

                    <div
                        id="photo-save-reminder"
                        @if ($attendance) hidden @endif
                    >
                        <p>
                            Save your plans above first to enable photo uploads.
                        </p>
                    </div>

                    <form
                        id="photo-form"
                        method="POST"
                        action="{{ route('ticket-photos.store', $event) }}"
                        enctype="multipart/form-data"
                        @if (!$attendance) hidden @endif
                    >
                        @csrf

                        <div class="photo-choice">
                            <label for="photo-file">
                                Choose an existing photo
                            </label>

                            <input
                                id="photo-file"
                                name="photo"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                            >
                        </div>

                        <div class="photo-choice">
                            <label for="photo-camera">
                                Or take a photo
                            </label>

                            <input
                                id="photo-camera"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                capture="environment"
                            >

                            <p class="hint">
                                Supported phones may open the rear camera.
                                Other devices may show a file picker.
                            </p>
                        </div>

                        <p
                            id="photo-feedback"
                            role="status"
                            aria-live="polite"
                        ></p>

                        <img
                            id="photo-preview"
                            class="photo-preview"
                            alt="Preview of the photo you selected"
                            hidden
                        >

                        <button id="upload-photo" type="submit">
                            Save photo
                        </button>
                    </form>
                </section>
            @endauth

            @include('events.reviews')
        </article>
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>

    <script>
        (() => {
            const attendanceForm =
                document.getElementById('attendance-form');

            if (attendanceForm) {
                attendanceForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const button =
                        document.getElementById('save-attendance');

                    const feedback =
                        document.getElementById('attendance-feedback');

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

                        const contentType =
                            response.headers.get('content-type') || '';

                        if (!contentType.includes('application/json')) {
                            throw new Error(
                                'Please refresh the page and sign in again.'
                            );
                        }

                        const data = await response.json();

                        if (!response.ok) {
                            const message = data.errors
                                ? Object.values(data.errors).flat().join(' ')
                                : data.message || 'Could not save your plans.';

                            throw new Error(message);
                        }

                        document.getElementById('attendance-count')
                            .textContent = data.attendance_count;

                        document.getElementById('saved-status')
                            .textContent = 'Your status: ' + data.status;

                        feedback.textContent = data.message;
                        feedback.className = 'feedback';
                        feedback.hidden = false;

                        const photoForm =
                            document.getElementById('photo-form');

                        const reminder =
                            document.getElementById('photo-save-reminder');

                        if (photoForm) {
                            photoForm.hidden = false;
                        }

                        if (reminder) {
                            reminder.hidden = true;
                        }
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

            const photoForm = document.getElementById('photo-form');

            if (!photoForm) {
                return;
            }

            const fileInput = document.getElementById('photo-file');
            const cameraInput = document.getElementById('photo-camera');
            const preview = document.getElementById('photo-preview');
            const feedback = document.getElementById('photo-feedback');
            const uploadButton = document.getElementById('upload-photo');

            let previewUrl = null;

            function clearPreview() {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                preview.hidden = true;
                preview.removeAttribute('src');
            }

            function selectPhoto(activeInput, otherInput) {
                const file = activeInput.files[0];

                clearPreview();
                otherInput.value = '';
                otherInput.removeAttribute('name');
                activeInput.name = 'photo';

                if (!file) {
                    feedback.textContent = 'No photo selected.';
                    feedback.className = 'hint';
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ];

                if (!allowedTypes.includes(file.type)) {
                    activeInput.value = '';
                    feedback.textContent =
                        'Choose a JPEG, PNG, or WebP photo.';
                    feedback.className = 'feedback error';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    activeInput.value = '';
                    feedback.textContent =
                        'Your photo must be 5 MB or smaller.';
                    feedback.className = 'feedback error';
                    return;
                }

                previewUrl = URL.createObjectURL(file);
                preview.src = previewUrl;
                preview.hidden = false;

                feedback.textContent = 'Photo selected: ' + file.name;
                feedback.className = 'hint';
            }

            fileInput.addEventListener('change', () => {
                selectPhoto(fileInput, cameraInput);
            });

            cameraInput.addEventListener('change', () => {
                selectPhoto(cameraInput, fileInput);
            });

            photoForm.addEventListener('submit', (event) => {
                const activeInput = fileInput.files.length
                    ? fileInput
                    : cameraInput;

                const file = activeInput.files[0];

                if (!file) {
                    event.preventDefault();

                    feedback.textContent =
                        'Choose or take a photo before saving.';
                    feedback.className = 'feedback error';
                    return;
                }

                // Submit exactly one photo field.
                fileInput.removeAttribute('name');
                cameraInput.removeAttribute('name');
                activeInput.name = 'photo';

                uploadButton.disabled = true;
                uploadButton.textContent = 'Uploading…';
            });

            window.addEventListener('pageshow', () => {
                uploadButton.disabled = false;
                uploadButton.textContent = 'Save photo';
            });
        })();
    </script>
</body>
</html>