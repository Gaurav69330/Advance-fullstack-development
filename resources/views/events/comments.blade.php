<section
    id="event-comments"
    class="panel"
    aria-labelledby="comments-heading"
    data-comments-url="{{ route('comments.index', $event) }}"
>
    <h2 id="comments-heading">Find your crowd.</h2>

    <p>Discuss the gig and arrange meetups with other fans.</p>

    <p class="hint">
        Comments are public. Showing the latest 50 comments.
        Updates refresh every 15 seconds while this tab is visible.
    </p>

    <div
        id="comments-list"
        aria-label="Event comments"
        aria-busy="true"
    >
        <p class="hint">Loading comments…</p>
    </div>

    <p
        id="comments-status"
        class="hint"
        role="status"
        aria-live="polite"
    ></p>

    @auth
        <form
            id="comment-form"
            method="POST"
            action="{{ route('comments.store', $event) }}"
        >
            @csrf

            <label for="comment-body">Join the conversation</label>

            <textarea
                id="comment-body"
                name="body"
                rows="4"
                minlength="2"
                maxlength="1000"
                required
                aria-describedby="comment-hint"
                placeholder="Who’s heading to the gig?"
            >{{ old('body') }}</textarea>

            <p id="comment-hint" class="hint">
                Write between 2 and 1,000 characters.
                Avoid posting private contact details.
            </p>

            <button id="post-comment" type="submit">
                Post comment
            </button>

            <p
                id="comment-feedback"
                role="status"
                aria-live="polite"
                hidden
            ></p>
        </form>
    @else
        <p>
            <a href="{{ route('login') }}">Sign in</a>
            to join the conversation.
        </p>
    @endauth
</section>

<style>
    .comment-entry {
        margin: 14px 0;
        padding: 16px;
        border: 1px solid #c8cdbd;
        border-radius: 6px;
        background: #fffef9;
    }

    .comment-entry p {
        margin: 8px 0;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .comment-entry time {
        display: block;
        color: #686b60;
        font-size: 12px;
    }

    #comment-body {
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
</style>

<script>
    (() => {
        const section = document.getElementById('event-comments');

        if (!section) {
            return;
        }

        const list = document.getElementById('comments-list');
        const status = document.getElementById('comments-status');
        const form = document.getElementById('comment-form');

        let loading = false;
        let previousResponse = null;
        let pollTimer = null;

        async function readResponse(response) {
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
                    : data.message || 'The request could not be completed.';

                throw new Error(message);
            }

            return data;
        }

        function renderComments(comments) {
            const fragment = document.createDocumentFragment();

            if (comments.length === 0) {
                const empty = document.createElement('p');

                empty.className = 'hint';
                empty.textContent =
                    'No comments yet. Start the conversation.';

                fragment.append(empty);
            }

            comments.forEach((comment) => {
                const article = document.createElement('article');
                article.className = 'comment-entry';

                const author = document.createElement('strong');
                author.textContent = comment.author;

                const body = document.createElement('p');
                body.textContent = comment.body;

                const time = document.createElement('time');
                time.dateTime = comment.created_at;
                time.textContent =
                    new Date(comment.created_at).toLocaleString();

                article.append(author, body, time);
                fragment.append(article);
            });

            list.replaceChildren(fragment);
        }

        async function loadComments() {
            if (loading || document.hidden) {
                return;
            }

            loading = true;
            list.setAttribute('aria-busy', 'true');

            try {
                const response = await fetch(section.dataset.commentsUrl, {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await readResponse(response);

                if (!Array.isArray(data.comments)) {
                    throw new Error('Unexpected comments response.');
                }

                const serialized = JSON.stringify(data.comments);

                if (serialized !== previousResponse) {
                    renderComments(data.comments);

                    status.textContent = previousResponse === null
                        ? 'Comments loaded.'
                        : 'Conversation updated.';

                    previousResponse = serialized;
                } else {
                    status.textContent = 'Comments are up to date.';
                }
            } catch (error) {
                status.textContent = error.message;
            } finally {
                loading = false;
                list.setAttribute('aria-busy', 'false');
            }
        }

        function stopPolling() {
            clearTimeout(pollTimer);
            pollTimer = null;
        }

        async function poll() {
            stopPolling();

            await loadComments();

            if (!document.hidden) {
                pollTimer = setTimeout(poll, 15000);
            }
        }

        if (form) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = document.getElementById('post-comment');
                const feedback =
                    document.getElementById('comment-feedback');

                button.disabled = true;
                button.textContent = 'Posting…';
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

                    document.getElementById('comment-body').value = '';

                    feedback.textContent = data.message;
                    feedback.className = 'feedback';
                    feedback.hidden = false;

                    await loadComments();
                } catch (error) {
                    feedback.textContent = error.message;
                    feedback.className = 'feedback error';
                    feedback.hidden = false;
                } finally {
                    button.disabled = false;
                    button.textContent = 'Post comment';
                }
            });
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
            } else {
                poll();
            }
        });

        window.addEventListener('pagehide', stopPolling);

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                poll();
            }
        });

        poll();
    })();
</script>