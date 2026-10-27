'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('keyword');

    if (!input || !input.dataset.suggestionsUrl) {
        return;
    }

    const container = document.createElement('div');
    container.className = 'search-suggestions';
    container.hidden = true;
    container.style.cssText = `
        background: #fffef9;
        border: 1px solid #c8cdbd;
        border-radius: 5px;
        padding: 12px;
        margin-top: 8px;
    `;

    const heading = document.createElement('p');
    heading.textContent = 'Matching saved gigs';
    heading.style.fontWeight = 'bold';

    const list = document.createElement('ul');
    list.style.cssText = `
        list-style: none;
        margin: 0;
        padding: 0;
    `;

    const status = document.createElement('p');
    status.className = 'hint';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');

    container.append(heading, list, status);
    input.insertAdjacentElement('afterend', container);

    let timer = null;
    let controller = null;
    let requestVersion = 0;

    function hideSuggestions() {
        container.hidden = true;
        list.replaceChildren();
        status.textContent = '';
    }

    function cancelPendingSearch() {
        clearTimeout(timer);
        controller?.abort();
        requestVersion++;
    }

    input.addEventListener('input', () => {
        cancelPendingSearch();

        const version = requestVersion;
        const query = input.value.trim();

        hideSuggestions();

        if (query.length < 2) {
            return;
        }

        timer = setTimeout(async () => {
            controller = new AbortController();

            container.hidden = false;
            status.textContent = 'Finding saved gigs…';

            try {
                const url = new URL(
                    input.dataset.suggestionsUrl,
                    window.location.origin
                );

                url.searchParams.set('q', query);

                const response = await fetch(url, {
                    signal: controller.signal,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Suggestions are unavailable.');
                }

                const data = await response.json();

                if (version !== requestVersion) {
                    return;
                }

                if (!Array.isArray(data.suggestions)) {
                    throw new Error('Unexpected suggestions response.');
                }

                list.replaceChildren();

                for (const suggestion of data.suggestions) {
                    const destination = new URL(
                        suggestion.url,
                        window.location.origin
                    );

                    if (destination.origin !== window.location.origin) {
                        continue;
                    }

                    const item = document.createElement('li');
                    const link = document.createElement('a');

                    link.href = destination.href;

                    link.textContent =
                        suggestion.title
                        + ' — ' + suggestion.city
                        + ' · ' + suggestion.date;

                    link.style.display = 'block';
                    link.style.padding = '10px 0';

                    item.append(link);
                    list.append(item);
                }

                status.textContent = list.children.length > 0
                    ? 'Use Tab to select a saved gig.'
                    : 'No saved matches. Submit your search to search Ticketmaster.';
            } catch (error) {
                if (
                    error.name === 'AbortError'
                    || version !== requestVersion
                ) {
                    return;
                }

                list.replaceChildren();

                status.textContent =
                    'Suggestions are unavailable. You can still submit your search.';
            }
        }, 350);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            cancelPendingSearch();
            hideSuggestions();
        }
    });

    document.addEventListener('click', (event) => {
        if (
            event.target !== input
            && !container.contains(event.target)
        ) {
            cancelPendingSearch();
            hideSuggestions();
        }
    });

    input.form?.addEventListener('submit', () => {
        cancelPendingSearch();
        hideSuggestions();
    });
});