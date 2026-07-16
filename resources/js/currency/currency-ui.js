import { convertCurrency, loadBdtRates, loadCurrencyMetadata } from './currency-api';

const form = document.getElementById('currency-converter-form');
const resultPanel = document.getElementById('currency-result');
const errorPanel = document.getElementById('currency-error');
const metadataPanel = document.getElementById('currency-metadata');
const swapButton = document.getElementById('swap-currencies');
const rateTableCard = document.querySelector('.exchange-rate-table-card');
const rateTableBody = document.getElementById('exchange-rate-table-body');
const rateTableStatus = document.getElementById('rate-table-status');
const rateTableError = document.getElementById('rate-table-error');

if (form && resultPanel && errorPanel && metadataPanel && swapButton) {
    const selects = form.querySelectorAll('select');

    loadCurrencyMetadata(form.dataset.metadataUrl, (error, metadata) => {
        metadataPanel.innerHTML = error
            ? '<p>Provider details unavailable.</p>'
            : `<p>${metadata.provider} · Base ${metadata.base_currency} · ${Object.keys(metadata.currencies).length} currencies</p>`;
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearError();
        resultPanel.innerHTML = '<h2>Converting...</h2><p>Fetching rates securely through Laravel.</p>';

        try {
            const result = await convertCurrency(form);
            renderResult(result);
        } catch (error) {
            showError(error.message);
            resultPanel.innerHTML = '<h2>Conversion unavailable</h2><p>Please review the form and try again.</p>';
        }
    });

    swapButton.addEventListener('click', () => {
        const [fromCurrency, toCurrency] = selects;
        const previousFromCurrency = fromCurrency.value;

        fromCurrency.value = toCurrency.value;
        toCurrency.value = previousFromCurrency;
    });

    import('./event-loop-demo')
        .then((module) => module.runEventLoopDemo())
        .catch((error) => {
            window.currencyEventLoopError = error.message;
        });
}

if (rateTableCard && rateTableBody && rateTableStatus && rateTableError) {
    loadBdtRates(rateTableCard.dataset.ratesUrl)
        .then(renderRateTable)
        .catch((error) => {
            rateTableStatus.textContent = 'Unavailable';
            rateTableStatus.classList.add('rejected');
            rateTableBody.innerHTML = '<tr><td colspan="5">No exchange rates are available right now.</td></tr>';
            rateTableError.hidden = false;
            rateTableError.textContent = error.message;
        });
}

function renderResult(result) {
    resultPanel.innerHTML = `
        <h2>${formatMoney(result.converted_amount)} ${result.to_currency}</h2>
        <dl class="detail-list single-column">
            <div>
                <dt>Original amount</dt>
                <dd>${formatMoney(result.amount)} ${result.from_currency}</dd>
            </div>
            <div>
                <dt>Exchange rate</dt>
                <dd>1 ${result.from_currency} = ${result.rate} ${result.to_currency}</dd>
            </div>
            <div>
                <dt>Last update</dt>
                <dd>${result.time_last_update_utc || 'Not provided'}</dd>
            </div>
            <div>
                <dt>Next update</dt>
                <dd>${result.time_next_update_utc || 'Not provided'}</dd>
            </div>
        </dl>
    `;
}

function renderRateTable(data) {
    rateTableStatus.textContent = `Base ${data.base_currency}`;
    rateTableBody.innerHTML = data.rates.map((rate) => `
        <tr>
            <td><strong>${rate.currency}</strong></td>
            <td>${rate.name}</td>
            <td>${formatRate(rate.rate)} ${rate.currency}</td>
            <td>${rate.bdt_equivalent ? `BDT ${formatMoney(rate.bdt_equivalent)}` : 'Not available'}</td>
            <td>${rate.updated_at || 'Not provided'}</td>
        </tr>
    `).join('');
}

function formatRate(value) {
    return Number(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 6,
    });
}

function showError(message) {
    errorPanel.hidden = false;
    errorPanel.textContent = message;
}

function clearError() {
    errorPanel.hidden = true;
    errorPanel.textContent = '';
}

function formatMoney(value) {
    return Number(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
