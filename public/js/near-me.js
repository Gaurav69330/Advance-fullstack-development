'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('live-search-form');
    const button = document.getElementById('near-me-button');
    const feedback = document.getElementById('location-status');

    if (!form || !button || !feedback) {
        return;
    }

    function showMessage(message, isError = false) {
        feedback.textContent = message;
        feedback.style.color = isError ? '#8b2727' : '#435d21';
    }

    function setHiddenField(name, value) {
        let input = form.querySelector(`input[name="${name}"]`);

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }

        input.value = String(value);
    }

    button.addEventListener('click', () => {
        if (!navigator.geolocation) {
            showMessage(
                'Location is unavailable. Search by city instead.',
                true
            );

            return;
        }

        button.disabled = true;
        button.textContent = 'Finding your location…';

        showMessage(
            'Allow location access when your browser asks.'
        );

        navigator.geolocation.getCurrentPosition(
            (position) => {
                setHiddenField(
                    'latitude',
                    position.coords.latitude
                );

                setHiddenField(
                    'longitude',
                    position.coords.longitude
                );

                setHiddenField('radius', 10);

                // Nearby searches use coordinates instead of a typed city.
                const city = form.querySelector('[name="city"]');
                const country = form.querySelector(
                    '[name="country_code"]'
                );

                if (city) {
                    city.value = '';
                }

                if (country) {
                    country.value = '';
                }

                // Start nearby results from the first page.
                form.querySelector('[name="page"]')?.remove();

                showMessage(
                    'Location found. Searching within 10 miles…'
                );

                button.disabled = false;
                button.textContent = 'Near me';

                form.requestSubmit();
            },
            (error) => {
                let message;

                switch (error.code) {
                    case 1:
                        message =
                            'Location permission was declined. '
                            + 'You can still search by city.';
                        break;

                    case 2:
                        message =
                            'Your location could not be determined. '
                            + 'Try again or search by city.';
                        break;

                    case 3:
                        message =
                            'Finding your location took too long. '
                            + 'Try again or search by city.';
                        break;

                    default:
                        message =
                            'Location is unavailable. '
                            + 'Search by city instead.';
                }

                showMessage(message, true);

                button.disabled = false;
                button.textContent = 'Near me';
            },
            {
                enableHighAccuracy: false,
                timeout: 10000,
                maximumAge: 300000,
            }
        );
    });
});