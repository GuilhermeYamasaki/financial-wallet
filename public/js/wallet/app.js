import { formatMoney, renderTransactions, renderUserLabel, renderUsersOptions } from './components.js';

const state = {
    token: localStorage.getItem('wallet_token'),
    email: localStorage.getItem('wallet_email'),
    users: [],
    wallet: null,
    transactions: [],
};

const elements = {
    authView: document.getElementById('auth-view'),
    walletView: document.getElementById('wallet-view'),
    sessionActions: document.getElementById('session-actions'),
    message: document.getElementById('message'),
    currentUser: document.getElementById('current-user'),
    walletBalance: document.getElementById('wallet-balance'),
    recipient: document.getElementById('recipient-id'),
    transactions: document.getElementById('transactions'),
};

function parseAmount(value) {
    const normalizedValue = String(value).replace(',', '.');
    const parsedValue = Number.parseFloat(normalizedValue);

    if (!Number.isFinite(parsedValue) || parsedValue <= 0) {
        throw new Error('Informe um valor maior que zero.');
    }

    return Math.round(parsedValue * 100);
}

function showMessage(text, type = 'success') {
    elements.message.textContent = text;
    elements.message.className = `alert ${type} show`;
}

function clearMessage() {
    elements.message.textContent = '';
    elements.message.className = 'alert';
}

function validationMessage(data) {
    if (data?.errors) {
        const firstError = Object.values(data.errors)[0];
        return Array.isArray(firstError) ? firstError[0] : firstError;
    }

    return data?.message || 'Não foi possível concluir a ação.';
}

async function api(path, options = {}) {
    const headers = {
        Accept: 'application/json',
        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
        ...(state.token ? { Authorization: `Bearer ${state.token}` } : {}),
    };

    const response = await fetch(path, {
        ...options,
        headers: {
            ...headers,
            ...(options.headers || {}),
        },
    });

    const text = await response.text();
    let data = {};

    try {
        data = text ? JSON.parse(text) : {};
    } catch (error) {
        data = { message: text || 'Resposta inválida da API.' };
    }

    if (response.status === 401) {
        clearSession();
        render();
    }

    if (!response.ok) {
        throw new Error(validationMessage(data));
    }

    return data;
}

function clearSession() {
    state.token = null;
    state.email = null;
    state.wallet = null;
    state.users = [];
    state.transactions = [];
    localStorage.removeItem('wallet_token');
    localStorage.removeItem('wallet_email');
}

async function login(email, password) {
    const data = await api('/api/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    });

    state.token = data.token;
    state.email = email;
    localStorage.setItem('wallet_token', state.token);
    localStorage.setItem('wallet_email', state.email);
}

async function loadWallet() {
    const [walletData, usersData, transactionsData] = await Promise.all([
        api('/api/wallet'),
        api('/api/users'),
        api('/api/transactions'),
    ]);

    state.wallet = walletData.wallet;
    state.users = usersData.users || [];
    state.transactions = transactionsData.transactions || [];
}

function render() {
    const authenticated = Boolean(state.token);

    elements.authView.classList.toggle('hidden', authenticated);
    elements.walletView.classList.toggle('hidden', !authenticated);
    elements.sessionActions.classList.toggle('hidden', !authenticated);

    if (!authenticated) {
        return;
    }

    elements.currentUser.textContent = renderUserLabel(state.users, state.email);
    elements.walletBalance.textContent = formatMoney(state.wallet?.balance);
    elements.recipient.innerHTML = renderUsersOptions(state.users);
    elements.transactions.innerHTML = renderTransactions(state.transactions);
}

async function refresh() {
    if (!state.token) {
        render();
        return;
    }

    clearMessage();
    await loadWallet();
    render();
}

document.getElementById('login-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessage();

    const formElement = event.currentTarget;
    const form = new FormData(formElement);

    try {
        await login(form.get('email'), form.get('password'));
        await refresh();
        showMessage('Login realizado com sucesso.');
        formElement.reset();
    } catch (error) {
        showMessage(error.message, 'error');
    }
});

document.getElementById('register-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessage();

    const formElement = event.currentTarget;
    const form = new FormData(formElement);
    const payload = {
        name: form.get('name'),
        email: form.get('email'),
        password: form.get('password'),
        password_confirmation: form.get('password_confirmation'),
    };

    try {
        await api('/api/register', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        await login(payload.email, payload.password);
        await refresh();
        showMessage('Cadastro realizado com sucesso.');
        formElement.reset();
    } catch (error) {
        showMessage(error.message, 'error');
    }
});

document.getElementById('deposit-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessage();

    const formElement = event.currentTarget;
    const form = new FormData(formElement);

    try {
        await api('/api/deposits', {
            method: 'POST',
            body: JSON.stringify({
                amount: parseAmount(form.get('amount')),
                description: form.get('description') || null,
            }),
        });
        await refresh();
        showMessage('Depósito realizado com sucesso.');
        formElement.reset();
    } catch (error) {
        showMessage(error.message, 'error');
    }
});

document.getElementById('transfer-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessage();

    const formElement = event.currentTarget;
    const form = new FormData(formElement);

    try {
        await api('/api/transfers', {
            method: 'POST',
            body: JSON.stringify({
                recipient_id: Number(form.get('recipient_id')),
                amount: parseAmount(form.get('amount')),
                description: form.get('description') || null,
            }),
        });
        await refresh();
        showMessage('Transferência realizada com sucesso.');
        formElement.reset();
    } catch (error) {
        showMessage(error.message, 'error');
    }
});

elements.transactions.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-reverse-id]');

    if (!button) {
        return;
    }

    clearMessage();
    button.disabled = true;

    try {
        await api(`/api/transactions/${button.dataset.reverseId}/reverse`, {
            method: 'POST',
        });
        await refresh();
        showMessage('Transação revertida com sucesso.');
    } catch (error) {
        showMessage(error.message, 'error');
        button.disabled = false;
    }
});

document.getElementById('logout-button').addEventListener('click', async () => {
    clearMessage();

    try {
        await api('/api/logout', { method: 'POST' });
    } catch (error) {
        showMessage(error.message, 'error');
    } finally {
        clearSession();
        render();
    }
});

document.getElementById('refresh-button').addEventListener('click', async () => {
    try {
        await refresh();
        showMessage('Dados atualizados.');
    } catch (error) {
        showMessage(error.message, 'error');
    }
});

refresh().catch((error) => {
    showMessage(error.message, 'error');
    render();
});
