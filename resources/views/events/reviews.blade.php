@php
    $eventReviews = $event->reviews()
        ->with('user')
        ->latest('updated_at')
        ->limit(20)
        ->get();

    $myReview = auth()->check()
        ? $event->reviews()
            ->where('user_id', auth()->id())
            ->first()
        : null;

    $reviewsOpen = !$event->event_date->isFuture();
@endphp

<section class="panel" aria-labelledby="reviews-heading">
    <h2 id="reviews-heading">Worth the noise?</h2>

    <p>Share what you thought of the gig.</p>

    <div id="review-list">
        @forelse ($eventReviews as $review)
            <article
                id="review-{{ $review->review_id }}"
                class="review-entry"
            >
                <strong>
                    {{ $review->user?->name ?? 'GigPulse member' }}
                </strong>

                <p class="review-rating">
                    {{ $review->rating }} / 5 stars
                </p>

                <p class="review-comment">{{ $review->comment }}</p>

                <time
                    class="hint"
                    datetime="{{ $review->updated_at->toIso8601String() }}"
                >
                    Updated {{ $review->updated_at->format('j M Y') }}
                </time>

                @if (
                    $reviewsOpen
                    && auth()->check()
                    && (int) $review->user_id === (int) auth()->id()
                )
                    <button
                        type="button"
                        data-edit-review
                        aria-controls="review-form"
                    >
                        Edit my review
                    </button>
                @endif
            </article>
        @empty
            <p id="no-reviews" class="hint">
                No reviews yet. Be the first to share your experience.
            </p>
        @endforelse
    </div>

    <p class="hint">Showing up to 20 recent reviews.</p>

    @if (!$reviewsOpen)
        <p class="notice">
            Reviews open after the event starts.
        </p>
    @endif

    @auth
        @if ($reviewsOpen)
            <form
                id="review-form"
                method="POST"
                action="{{ route('reviews.store', $event) }}"
                data-review-id="{{ $myReview?->review_id }}"
                data-delete-url="{{ route('reviews.destroy', ['review' => 0]) }}"
            >
                @csrf

                <h3 id="review-form-heading">
                    {{ $myReview ? 'Edit your review' : 'Your review' }}
                </h3>

                <label for="review-rating">Rating</label>

                <select id="review-rating" name="rating" required>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option
                            value="{{ $rating }}"
                            @selected(
                                (int) old('rating', $myReview?->rating ?? 5)
                                === $rating
                            )
                        >
                            {{ $rating }}
                            {{ $rating === 1 ? 'star' : 'stars' }}
                        </option>
                    @endfor
                </select>

                <label
                    for="review-comment"
                    class="review-comment-label"
                >
                    Tell us about the gig
                </label>

                <textarea
                    id="review-comment"
                    name="comment"
                    rows="5"
                    minlength="5"
                    maxlength="2000"
                    required
                    aria-describedby="review-hint"
                >{{ old('comment', $myReview?->comment ?? '') }}</textarea>

                <p class="hint" id="review-hint">
                    Write between 5 and 2,000 characters.
                    Saving again updates your existing review.
                </p>

                <div class="review-actions">
                    <button id="save-review" type="submit">
                        Save review
                    </button>

                    <button
                        id="delete-review"
                        class="review-delete-button"
                        type="button"
                        @if (!$myReview) hidden @endif
                    >
                        Delete my review
                    </button>
                </div>
            </form>
        @elseif ($myReview)
            <form
                method="POST"
                action="{{ route('reviews.destroy', $myReview) }}"
                onsubmit="return confirm('Delete your review permanently? This cannot be undone.');"
            >
                @csrf
                @method('DELETE')

                <button
                    class="review-delete-button"
                    type="submit"
                >
                    Delete my review
                </button>
            </form>
        @endif

        <p
            id="review-feedback"
            role="status"
            aria-live="polite"
            hidden
        ></p>
    @else
        <p>
            <a href="{{ route('login') }}">Sign in</a>
            to manage your reviews.
        </p>
    @endauth
</section>

<style>
    .review-entry {
        margin: 16px 0;
        padding: 18px;
        border: 1px solid #c8cdbd;
        border-radius: 6px;
        background: #fffef9;
    }

    .review-rating {
        margin: 6px 0;
        font-weight: bold;
    }

    .review-comment {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .review-entry time {
        display: block;
    }

    .review-comment-label {
        margin-top: 18px;
    }

    #review-form {
        scroll-margin-top: 30px;
    }

    #review-comment {
        display: block;
        width: 100%;
        padding: 12px;
        border: 1px solid #bbc5a8;
        border-radius: 5px;
        background: white;
        color: #20231e;
        font: inherit;
        resize: vertical;
    }

    .review-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .review-delete-button {
        background: #873c32 !important;
        border-color: #873c32 !important;
        color: white;
    }

    #delete-review[hidden] {
        display: none !important;
    }
</style>

<script>
    (() => {
        const form = document.getElementById('review-form');

        if (!form) {
            return;
        }

        const list = document.getElementById('review-list');
        const saveButton = document.getElementById('save-review');
        const deleteButton = document.getElementById('delete-review');
        const feedback = document.getElementById('review-feedback');
        const textarea = document.getElementById('review-comment');
        const ratingInput = document.getElementById('review-rating');
        const heading = document.getElementById('review-form-heading');

        let busy = false;

        function showFeedback(message, isError = false) {
            feedback.textContent = message;

            feedback.className = isError
                ? 'feedback error'
                : 'feedback';

            feedback.hidden = false;
        }

        function setBusy(value) {
            busy = value;

            saveButton.disabled = value;
            deleteButton.disabled = value;

            list.querySelectorAll('[data-edit-review]')
                .forEach((button) => {
                    button.disabled = value;
                });
        }

        async function readResponse(response) {
            if (response.status === 429) {
                throw new Error(
                    'Too many attempts. Please wait a minute and try again.'
                );
            }

            if (response.status === 401 || response.status === 419) {
                throw new Error(
                    'Your session expired. Refresh and sign in again.'
                );
            }

            const contentType =
                response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                throw new Error(
                    'Please refresh the page and try again.'
                );
            }

            const data = await response.json();

            if (!response.ok) {
                const message = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message || 'The request failed.';

                throw new Error(message);
            }

            return data;
        }

        function showReview(review) {
            document.getElementById('no-reviews')?.remove();

            let article = document.getElementById(
                'review-' + review.review_id
            );

            if (!article) {
                article = document.createElement('article');
                article.id = 'review-' + review.review_id;
                article.className = 'review-entry';
            }

            const author = document.createElement('strong');
            author.textContent = review.author;

            const rating = document.createElement('p');
            rating.className = 'review-rating';
            rating.textContent = review.rating + ' / 5 stars';

            const comment = document.createElement('p');
            comment.className = 'review-comment';
            comment.textContent = review.comment;

            const updated = document.createElement('time');
            updated.className = 'hint';
            updated.dateTime = review.updated_at;

            updated.textContent =
                'Updated ' + new Date(review.updated_at)
                    .toLocaleDateString();

            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.textContent = 'Edit my review';
            editButton.setAttribute('data-edit-review', '');
            editButton.setAttribute('aria-controls', 'review-form');
            editButton.disabled = busy;

            article.replaceChildren(
                author,
                rating,
                comment,
                updated,
                editButton
            );

            list.prepend(article);

            list.querySelectorAll('.review-entry')
                .forEach((entry, index) => {
                    if (index >= 20) {
                        entry.remove();
                    }
                });
        }

        // Edit moves the user to their existing prefilled review form.
        list.addEventListener('click', (event) => {
            if (!(event.target instanceof Element)) {
                return;
            }

            const editButton =
                event.target.closest('[data-edit-review]');

            if (!editButton || busy) {
                return;
            }

            heading.textContent = 'Edit your review';

            const reduceMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

            form.scrollIntoView({
                behavior: reduceMotion ? 'auto' : 'smooth',
                block: 'center',
            });

            textarea.focus({
                preventScroll: true,
            });

            showFeedback(
                'Change your rating or review, then click Save review.'
            );
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (busy) {
                return;
            }

            if (textarea.value.trim().length < 5) {
                showFeedback(
                    'Write at least five characters.',
                    true
                );

                textarea.focus();
                return;
            }

            setBusy(true);
            saveButton.textContent = 'Saving…';
            feedback.hidden = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await readResponse(response);

                form.dataset.reviewId = data.review.review_id;
                textarea.value = data.review.comment;
                ratingInput.value = String(data.review.rating);

                heading.textContent = 'Edit your review';
                deleteButton.hidden = false;

                showReview(data.review);
                showFeedback(data.message);
            } catch (error) {
                showFeedback(error.message, true);
            } finally {
                setBusy(false);
                saveButton.textContent = 'Save review';
            }
        });

        deleteButton.addEventListener('click', async () => {
            if (busy || !form.dataset.reviewId) {
                return;
            }

            const confirmed = window.confirm(
                'Delete your review permanently? This cannot be undone.'
            );

            if (!confirmed) {
                return;
            }

            const id = Number(form.dataset.reviewId);

            if (!Number.isSafeInteger(id) || id < 1) {
                showFeedback('Invalid review ID.', true);
                return;
            }

            setBusy(true);
            deleteButton.textContent = 'Deleting…';
            feedback.hidden = true;

            try {
                const url = form.dataset.deleteUrl.replace(
                    /\/0$/,
                    '/' + id
                );

                const body = new URLSearchParams();

                body.set(
                    '_token',
                    form.querySelector('[name="_token"]').value
                );

                body.set('_method', 'DELETE');

                const response = await fetch(url, {
                    method: 'POST',
                    body,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await readResponse(response);

                document.getElementById('review-' + id)?.remove();

                form.dataset.reviewId = '';
                textarea.value = '';
                ratingInput.value = '5';

                heading.textContent = 'Your review';
                deleteButton.hidden = true;

                if (!list.querySelector('.review-entry')) {
                    const empty = document.createElement('p');

                    empty.id = 'no-reviews';
                    empty.className = 'hint';

                    empty.textContent =
                        'No reviews yet. Be the first to share your experience.';

                    list.replaceChildren(empty);
                }

                showFeedback(data.message);
            } catch (error) {
                showFeedback(error.message, true);
            } finally {
                setBusy(false);
                deleteButton.textContent = 'Delete my review';
            }
        });
    })();
</script>