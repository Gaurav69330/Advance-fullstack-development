<section
    id="event-comments"
    class="panel"
    aria-labelledby="comments-heading"
    data-comments-url="{{ route('comments.index', $event) }}"
    data-update-url="{{ route('comments.update', ['comment' => 0]) }}"
    data-delete-url="{{ route('comments.destroy', ['comment' => 0]) }}"
    data-csrf="{{ csrf_token() }}"
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

    .comment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    #comment-body,
    .comment-edit-input {
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

    .comment-edit-form {
        margin-top: 15px;
    }

    .comment-delete-button {
        background: #873c32 !important;
        border-color: #873c32 !important;
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
        const postForm = document.getElementById('comment-form');

        let comments = [];
        let previousResponse = null;
        let editingId = null;
        let mutationBusy = false;
        let loading = false;
        let pollTimer = null;
        let fetchController = null;
        let retryDelay = 15000;

        function stopPolling() {
            clearTimeout(pollTimer);
            pollTimer = null;
        }

        function pauseUpdates() {
            stopPolling();
            fetchController?.abort();
        }

        function commentUrl(template, id) {
            const number = Number(id);

            if (!Number.isSafeInteger(number) || number < 1) {
                throw new Error('Invalid comment ID.');
            }

            return template.replace(/\/0$/, '/' + number);
        }

        async function readResponse(response) {
            if (response.status === 429) {
                const seconds = Number(
                    response.headers.get('Retry-After') || 60
                );

                throw new Error(
                    'Too many requests. Try again in '
                    + seconds + ' seconds.'
                );
            }

            if (response.status === 401 || response.status === 419) {
                throw new Error(
                    'Your session expired. Refresh the page and sign in again.'
                );
            }

            const type = response.headers.get('content-type') || '';

            if (!type.includes('application/json')) {
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

        async function sendChange(url, method, fields = {}) {
            const body = new URLSearchParams();

            body.set('_token', section.dataset.csrf);

            if (method !== 'POST') {
                body.set('_method', method);
            }

            Object.entries(fields).forEach(([key, value]) => {
                body.set(key, value);
            });

            const response = await fetch(url, {
                method: 'POST',
                body,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            return readResponse(response);
        }

        function makeButton(text, handler, className = '') {
            const button = document.createElement('button');

            button.type = 'button';
            button.textContent = text;
            button.className = className;
            button.disabled = mutationBusy;
            button.addEventListener('click', handler);

            return button;
        }

        function renderComments() {
            const fragment = document.createDocumentFragment();

            if (!comments.length) {
                const empty = document.createElement('p');

                empty.className = 'hint';
                empty.textContent =
                    'No comments yet. Start the conversation.';

                fragment.append(empty);
            }

            comments.forEach((comment) => {
                const article = document.createElement('article');

                article.className = 'comment-entry';
                article.id = 'comment-' + comment.comment_id;

                const author = document.createElement('strong');
                author.textContent = comment.author;

                const body = document.createElement('p');
                body.textContent = comment.body;

                const time = document.createElement('time');

                const wasEdited = comment.updated_at
                    && comment.updated_at !== comment.created_at;

                const timestamp = wasEdited
                    ? comment.updated_at
                    : comment.created_at;

                time.dateTime = timestamp;
                time.textContent =
                    (wasEdited ? 'Edited ' : 'Posted ')
                    + new Date(timestamp).toLocaleString();

                article.append(author, body, time);

                if (comment.can_manage) {
                    const actions = document.createElement('div');

                    actions.className = 'comment-actions';

                    actions.append(
                        makeButton(
                            'Edit',
                            () => startEditing(comment, article)
                        ),
                        makeButton(
                            'Delete',
                            () => deleteComment(comment),
                            'comment-delete-button'
                        )
                    );

                    article.append(actions);
                }

                fragment.append(article);
            });

            list.replaceChildren(fragment);
        }

        async function loadComments() {
            if (
                loading
                || document.hidden
                || editingId !== null
                || mutationBusy
            ) {
                return;
            }

            loading = true;
            list.setAttribute('aria-busy', 'true');

            const controller = new AbortController();
            fetchController = controller;

            try {
                const response = await fetch(section.dataset.commentsUrl, {
                    signal: controller.signal,
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

                if (editingId !== null || mutationBusy) {
                    return;
                }

                const serialized = JSON.stringify(data.comments);

                if (serialized !== previousResponse) {
                    comments = data.comments;
                    renderComments();

                    status.textContent = previousResponse === null
                        ? 'Comments loaded.'
                        : 'Conversation updated.';

                    previousResponse = serialized;
                }

                retryDelay = 15000;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    status.textContent = error.message;
                    retryDelay = 60000;
                }
            } finally {
                loading = false;

                if (fetchController === controller) {
                    fetchController = null;
                }

                list.setAttribute('aria-busy', 'false');
            }
        }

        async function poll() {
            stopPolling();

            await loadComments();

            if (
                !document.hidden
                && editingId === null
                && !mutationBusy
            ) {
                stopPolling();
                pollTimer = setTimeout(poll, retryDelay);
            }
        }

        function startEditing(comment, article) {
            if (mutationBusy || editingId !== null) {
                return;
            }

            editingId = comment.comment_id;
            pauseUpdates();

            const originalBody = article.querySelector('p');
            const originalActions =
                article.querySelector('.comment-actions');

            originalBody.hidden = true;
            originalActions.hidden = true;

            const editForm = document.createElement('form');
            editForm.className = 'comment-edit-form';

            const label = document.createElement('label');
            label.htmlFor = 'edit-comment-' + comment.comment_id;
            label.textContent = 'Edit your comment';

            const textarea = document.createElement('textarea');

            textarea.id = label.htmlFor;
            textarea.className = 'comment-edit-input';
            textarea.value = comment.body;
            textarea.rows = 4;
            textarea.minLength = 2;
            textarea.maxLength = 1000;
            textarea.required = true;

            const actions = document.createElement('div');
            actions.className = 'comment-actions';

            const saveButton = document.createElement('button');
            saveButton.type = 'submit';
            saveButton.textContent = 'Save changes';

            const cancelButton = makeButton('Cancel', () => {
                editingId = null;
                editForm.remove();

                originalBody.hidden = false;
                originalActions.hidden = false;

                originalActions.querySelector('button')?.focus();

                status.textContent = 'Editing cancelled.';
                poll();
            });

            const feedback = document.createElement('p');
            feedback.className = 'hint';
            feedback.setAttribute('role', 'status');

            actions.append(saveButton, cancelButton);
            editForm.append(label, textarea, actions, feedback);
            article.append(editForm);

            status.textContent =
                'Automatic updates paused while you edit.';

            textarea.focus();

            editForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const body = textarea.value.trim();

                if (body.length < 2) {
                    feedback.textContent =
                        'Write at least two characters.';
                    return;
                }

                mutationBusy = true;
                saveButton.disabled = true;
                cancelButton.disabled = true;
                saveButton.textContent = 'Saving…';

                try {
                    const data = await sendChange(
                        commentUrl(
                            section.dataset.updateUrl,
                            comment.comment_id
                        ),
                        'PATCH',
                        { body }
                    );

                    comments = comments.map((entry) => {
                        return entry.comment_id === comment.comment_id
                            ? data.comment
                            : entry;
                    });

                    editingId = null;
                    mutationBusy = false;
                    previousResponse = null;

                    renderComments();
                    status.textContent = data.message;

                    document.getElementById(
                        'comment-' + comment.comment_id
                    )?.querySelector('button')?.focus();

                    poll();
                } catch (error) {
                    feedback.textContent = error.message;
                } finally {
                    mutationBusy = false;
                    saveButton.disabled = false;
                    cancelButton.disabled = false;
                    saveButton.textContent = 'Save changes';
                }
            });
        }

        async function deleteComment(comment) {
            if (mutationBusy || editingId !== null) {
                return;
            }

            const confirmed = window.confirm(
                'Delete this comment permanently? This cannot be undone.'
            );

            if (!confirmed) {
                return;
            }

            mutationBusy = true;
            pauseUpdates();
            renderComments();

            try {
                const data = await sendChange(
                    commentUrl(
                        section.dataset.deleteUrl,
                        comment.comment_id
                    ),
                    'DELETE'
                );

                comments = comments.filter(
                    (entry) => entry.comment_id !== comment.comment_id
                );

                previousResponse = null;
                status.textContent = data.message;
            } catch (error) {
                status.textContent = error.message;
            } finally {
                mutationBusy = false;
                renderComments();
                poll();
            }
        }

        if (postForm) {
            postForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const feedback =
                    document.getElementById('comment-feedback');

                if (mutationBusy || editingId !== null) {
                    feedback.textContent =
                        'Finish editing your comment first.';
                    feedback.className = 'feedback error';
                    feedback.hidden = false;
                    return;
                }

                const button = document.getElementById('post-comment');
                const textarea = document.getElementById('comment-body');
                const body = textarea.value.trim();

                if (body.length < 2) {
                    feedback.textContent =
                        'Write at least two characters.';
                    feedback.className = 'feedback error';
                    feedback.hidden = false;
                    return;
                }

                mutationBusy = true;
                pauseUpdates();

                button.disabled = true;
                button.textContent = 'Posting…';
                feedback.hidden = true;

                try {
                    const data = await sendChange(
                        postForm.action,
                        'POST',
                        { body }
                    );

                    textarea.value = '';

                    comments = comments.filter(
                        (entry) =>
                            entry.comment_id !== data.comment.comment_id
                    );

                    comments.push(data.comment);

                    comments.sort(
                        (a, b) => a.comment_id - b.comment_id
                    );

                    comments = comments.slice(-50);
                    previousResponse = null;

                    feedback.textContent = data.message;
                    feedback.className = 'feedback';
                    feedback.hidden = false;
                } catch (error) {
                    feedback.textContent = error.message;
                    feedback.className = 'feedback error';
                    feedback.hidden = false;
                } finally {
                    mutationBusy = false;
                    button.disabled = false;
                    button.textContent = 'Post comment';

                    renderComments();
                    poll();
                }
            });
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                pauseUpdates();
            } else {
                poll();
            }
        });

        window.addEventListener('pagehide', pauseUpdates);

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                poll();
            }
        });

        poll();
    })();
</script>