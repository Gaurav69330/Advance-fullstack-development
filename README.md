# GigPulse

GigPulse is a live music discovery and gig diary web app. Search Ticketmaster listings by artist, city, or country, preview an event from Discover, and sign in to save it to **My gigs**.

## Features

- Browse Ticketmaster events, view available artwork and venue details, and follow ticket links.
- Use **Near me** to search for gigs within 10 miles. Restore your previous Discover filters with **Use last search**.
- Track attendance, write reviews, and post event comments. Search suggestions, attendance, comments, and reviews use JavaScript Fetch to update the page. Comments refresh periodically.
- Capture or upload a ticket stub or concert photo. Saved photos are private to the account that uploaded them.
- Browse on a phone using the responsive layout and collapsible navigation.

## Run locally

You need PHP 8.4.1 or later with PDO MySQL enabled, Composer, and MySQL or MariaDB. On Windows you can start the database from the XAMPP Control Panel.

1. Open a terminal in the cloned or extracted GigPulse folder and run:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   If you already have a configured `.env`, keep it and skip the copy and key generation steps.

2. Create an empty database named `gigpulse_laravel`. Set the following values in `.env`, using the credentials for your local database and your own Ticketmaster Discovery API key.

   ```dotenv
   APP_NAME=GigPulse
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gigpulse_laravel
   DB_USERNAME=root
   DB_PASSWORD=

   TICKETMASTER_API_KEY=your_consumer_key
   ```

   Keep `.env` out of Git. The Ticketmaster key is read by Laravel on the server.

3. Run the migrations and start Laravel.

   ```powershell
   php artisan migrate
   php artisan serve
   ```

4. Open [GigPulse](http://127.0.0.1:8000/). Register an account to save events and use **My gigs**.

To add sample events to a development database, run `php artisan db:seed --class=GigPulseSeeder`.

## Coursework demo

1. Search on Discover and click an event title to open its public preview. Sign in and save an event, then reopen it from **My gigs**.
2. On a saved event, change attendance or post a comment. In Firefox DevTools, use **Network → XHR** to see the Fetch requests. Open the event in a second tab to demonstrate automatic comment refresh.
3. Show the mobile menu, restore a previous search, run **Near me**, and capture and save a photo. Use localhost on the PC or a trusted HTTPS address on a phone for geolocation and live camera access.

## Project structure

```text
GigPulse/
├── app/
│   ├── Http/Controllers/    # Web requests and responses
│   ├── Models/              # Eloquent records and relationships
│   ├── Providers/           # Application setup and rate limits
│   └── Services/            # Ticketmaster integration
├── bootstrap/               # Laravel startup and middleware configuration
├── config/                  # Application, database, storage, and service settings
├── database/
│   ├── migrations/          # Tables, columns, constraints, and indexes
│   └── seeders/             # Sample data for local development
├── public/
│   ├── index.php            # Web entry point
│   └── js/                  # Discover page browser scripts
├── resources/views/         # Blade templates
├── routes/                  # Web routes and console commands
├── storage/                 # Runtime files, logs, and private uploads
└── tests/                   # Laravel feature and unit test suites
```

### Routes, controllers, and services

| File | What it does |
| --- | --- |
| `routes/web.php` | Maps page URLs and form actions to controllers and applies authentication and rate limits. |
| `routes/console.php` | Registers custom Artisan console commands. |
| `app/Http/Controllers/Controller.php` | Base controller for the application. |
| `app/Http/Controllers/LiveEventController.php` | Handles Discover search, pagination, and public Ticketmaster event previews. |
| `app/Http/Controllers/SaveLiveEventController.php` | Imports a selected Ticketmaster event and venue, saves available artwork, and adds the event to My gigs. |
| `app/Http/Controllers/DiscoveryController.php` | Handles bulk import of Ticketmaster listings. |
| `app/Http/Controllers/EventController.php` | Displays saved listings and individual saved event pages. |
| `app/Http/Controllers/AuthController.php` | Handles registration, sign-in, and sign-out. |
| `app/Http/Controllers/MyGigController.php` | Displays the signed-in user's gig diary. |
| `app/Http/Controllers/AttendanceController.php` | Saves attendance status and returns updated attendance information. |
| `app/Http/Controllers/SearchSuggestionController.php` | Returns autocomplete matches from upcoming saved events. |
| `app/Http/Controllers/CommentController.php` | Lists event comments and handles posting, editing, and deleting them. |
| `app/Http/Controllers/ReviewController.php` | Creates, updates, and deletes event reviews. |
| `app/Http/Controllers/TicketPhotoController.php` | Validates, stores, and serves private event photos after checking ownership. |
| `app/Services/TicketmasterService.php` | Sends server-side search and event-detail requests to Ticketmaster and caches responses. |
| `app/Providers/AppServiceProvider.php` | Configures application services, including rate limits for comment actions. |

### Models and database

| File | What it does |
| --- | --- |
| `app/Models/User.php` | Represents a registered user. |
| `app/Models/Event.php` | Represents a gig and links it to its venue, attendance records, comments, and reviews. |
| `app/Models/Venue.php` | Stores venue information, including coordinates, country, and timezone. |
| `app/Models/Attendance.php` | Connects a user to an event and stores their attendance status and private photo path. |
| `app/Models/Comment.php` | Represents a comment posted by a user on an event. |
| `app/Models/Review.php` | Represents a user's event rating and review text. |
| `database/migrations/` | Builds the database schema and adds later changes such as Ticketmaster IDs, comments, indexes, and artwork URLs. |
| `database/seeders/GigPulseSeeder.php` | Adds sample gig data for local testing. |

### Templates and browser scripts

| File | What it does |
| --- | --- |
| `resources/views/live-events/index.blade.php` | Renders Discover, search filters, and Ticketmaster results. |
| `resources/views/live-events/show.blade.php` | Renders an event preview visitors can open before saving. |
| `resources/views/events/index.blade.php` | Lists events saved in the GigPulse database. |
| `resources/views/events/show.blade.php` | Displays a saved event, its artwork, attendance controls, and the photo, comments, and review sections. |
| `resources/views/events/camera.blade.php` | Provides camera preview and capture controls for an event photo. |
| `resources/views/events/comments.blade.php` | Renders comments and uses Fetch to submit changes and poll for updates. |
| `resources/views/events/reviews.blade.php` | Renders the review list and Fetch-powered review controls. |
| `resources/views/my-gigs/index.blade.php` | Shows the signed-in user's saved events. |
| `resources/views/auth/login.blade.php` and `register.blade.php` | Render the sign-in and registration forms. |
| `resources/views/partials/header.blade.php` | Provides shared navigation and the responsive menu. |
| `public/js/near-me.js` | Requests browser geolocation and submits a nearby search. |
| `public/js/search-preferences.js` | Saves and restores Discover filters using localStorage. |
| `public/js/search-suggestions.js` | Fetches and displays search suggestions as the user types. |

Coursework verification tasks are tracked in GitHub Issues under the **v1.0 — Coursework demonstration** milestone.
