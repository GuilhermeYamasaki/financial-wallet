const moneyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

export function formatMoney(cents) {
    return moneyFormatter.format((Number(cents) || 0) / 100);
}

export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[character]));
}

export function renderUserLabel(users, email) {
    const currentUser = users.find((user) => user.email === email);

    return currentUser
        ? `${currentUser.name} (${currentUser.email})`
        : email || 'Usuário autenticado';
}

export function renderUsersOptions(users) {
    return users.map((user) => `
        <option value="${user.id}">${escapeHtml(user.name)} - ${escapeHtml(user.email)}</option>
    `).join('');
}

export function renderTransactions(transactions) {
    if (!transactions.length) {
        return '<p class="muted">Nenhuma transação encontrada.</p>';
    }

    return transactions.map((transaction) => `
        <article class="transaction">
            <div>
                <strong>${transactionTitle(transaction)}</strong>
                <div class="transaction-meta">
                    <span>#${transaction.id}</span>
                    <span>${transactionPeople(transaction)}</span>
                    <span class="status">${escapeHtml(transaction.status)}</span>
                </div>
                ${transaction.description ? `<div class="muted">${escapeHtml(transaction.description)}</div>` : ''}
            </div>
            <div>
                <div class="amount">${formatMoney(transaction.amount)}</div>
                ${canReverse(transaction) ? `<button class="button secondary" data-reverse-id="${transaction.id}" type="button">Reverter</button>` : ''}
            </div>
        </article>
    `).join('');
}

function transactionTitle(transaction) {
    if (transaction.type === 'deposit') {
        return 'Depósito';
    }

    if (transaction.type === 'reversal') {
        return 'Reversão';
    }

    return 'Transferência';
}

function transactionPeople(transaction) {
    const from = transaction.wallet_from?.user;
    const to = transaction.wallet_to?.user;

    if (from && to) {
        return `${escapeHtml(from.name)} para ${escapeHtml(to.name)}`;
    }

    if (to) {
        return `Entrada para ${escapeHtml(to.name)}`;
    }

    if (from) {
        return `Saída de ${escapeHtml(from.name)}`;
    }

    return 'Movimentação da carteira';
}

function canReverse(transaction) {
    return transaction.status !== 'reversed' && transaction.type !== 'reversal';
}
