'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('live-search-form');

    if (!form) {
        return;
    }

    const cityInput = form.querySelector('[name="city"]');
    const countryInput = form.querySelector('[name="country_code"]');
    const actions = form.querySelector('.search-actions');
    const feedback = document.getElementById('search-status');

    if (!cityInput || !countryInput || !actions) {
        return;
    }

    const storageKey = 'gigpulse.search-preferences.v1';

    let savedPreferences = null;

    try {
        const stored = JSON.parse(
            localStorage.getItem(storageKey) || 'null'
        );

        if (
            stored
            && typeof stored.city === 'string'
            && typeof stored.country === 'string'
            && stored.city.length <= 100
            && (
                stored.country === ''
                || /^[A-Z]{2}$/.test(stored.country)
            )
        ) {
            savedPreferences = stored;
        }
    } catch {
        // Search still works when browser storage is unavailable.
    }

    form.addEventListener('submit', () => {
        // Do not store GPS coordinates or overwrite preferences
        // when the user searches using Near me.
        if (
            form.querySelector('[name="latitude"]')
            || form.querySelector('[name="longitude"]')
        ) {
            return;
        }

        const preferences = {
            city: cityInput.value.trim(),
            country: countryInput.value.trim().toUpperCase(),
        };

        if (preferences.country === 'UK') {
            preferences.country = 'GB';
        }

        try {
            localStorage.setItem(
                storageKey,
                JSON.stringify(preferences)
            );
        } catch {
            // Storage failure must not prevent submitting a search.
        }
    });

    if (
        !savedPreferences
        || (!savedPreferences.city && !savedPreferences.country)
    ) {
        return;
    }

    const restoreButton = document.createElement('button');
    restoreButton.type = 'button';
    restoreButton.textContent = 'Use last search';

    const forgetButton = document.createElement('button');
    forgetButton.type = 'button';
    forgetButton.textContent = 'Forget saved filters';

    actions.append(restoreButton, forgetButton);

    restoreButton.addEventListener('click', () => {
        cityInput.value = savedPreferences.city;
        countryInput.value = savedPreferences.country;

        // Switch from nearby search to the saved city/country.
        ['latitude', 'longitude', 'radius', 'page'].forEach((name) => {
            form.querySelector(`[name="${name}"]`)?.remove();
        });

        form.requestSubmit();
    });

    forgetButton.addEventListener('click', () => {
        try {
            localStorage.removeItem(storageKey);

            savedPreferences = null;
            restoreButton.remove();
            forgetButton.remove();

            if (feedback) {
                feedback.textContent = 'Saved search filters removed.';
            }
        } catch {
            if (feedback) {
                feedback.textContent =
                    'Your browser could not remove the saved filters.';
            }
        }
    });
});