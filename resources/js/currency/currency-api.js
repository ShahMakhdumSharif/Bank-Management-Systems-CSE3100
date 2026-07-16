export async function convertCurrency(form) {
    const response = await fetch(form.action, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
        },
        body: new FormData(form),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message || firstValidationError(payload.errors) || 'Currency conversion failed.');
    }

    return payload.data;
}

export async function loadBdtRates(url) {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
        },
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message || 'Exchange-rate table could not be loaded.');
    }

    return payload.data;
}

export function loadCurrencyMetadata(url, callback) {
    const request = new XMLHttpRequest();

    request.open('GET', url);
    request.setRequestHeader('Accept', 'application/json');

    request.onload = () => {
        if (request.status >= 200 && request.status < 300) {
            callback(null, JSON.parse(request.responseText));
            return;
        }

        callback(new Error('Currency metadata could not be loaded.'));
    };

    request.onerror = () => callback(new Error('Currency metadata could not be loaded.'));
    request.send();
}

function firstValidationError(errors = {}) {
    const firstKey = Object.keys(errors)[0];

    return firstKey ? errors[firstKey][0] : null;
}
