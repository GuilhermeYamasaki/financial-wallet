<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Financial Wallet</title>
        <link rel="stylesheet" href="{{ asset('css/wallet.css') }}">
        <script type="module" src="{{ asset('js/wallet/app.js') }}"></script>
    </head>
    <body>
        <main class="page">
            <div class="shell">
                <header class="topbar">
                    <div class="brand">
                        <h1>Financial Wallet</h1>
                        <span>Carteira financeira</span>
                    </div>

                    <div class="actions hidden" id="session-actions">
                        <button class="button secondary" id="refresh-button" type="button">Atualizar</button>
                        <button class="button danger" id="logout-button" type="button">Sair</button>
                    </div>
                </header>

                <div class="alert" id="message"></div>

                <section class="grid auth-grid" id="auth-view">
                    <div class="card">
                        <h2>Entrar</h2>
                        <form class="form" id="login-form">
                            <div class="field">
                                <label for="login-email">E-mail</label>
                                <input class="input" id="login-email" name="email" type="email" autocomplete="email" required>
                            </div>

                            <div class="field">
                                <label for="login-password">Senha</label>
                                <input class="input" id="login-password" name="password" type="password" autocomplete="current-password" required>
                            </div>

                            <button class="button" type="submit">Entrar</button>
                        </form>
                    </div>

                    <div class="card">
                        <h2>Criar conta</h2>
                        <form class="form" id="register-form">
                            <div class="field">
                                <label for="register-name">Nome</label>
                                <input class="input" id="register-name" name="name" type="text" autocomplete="name" required>
                            </div>

                            <div class="field">
                                <label for="register-email">E-mail</label>
                                <input class="input" id="register-email" name="email" type="email" autocomplete="email" required>
                            </div>

                            <div class="field">
                                <label for="register-password">Senha</label>
                                <input class="input" id="register-password" name="password" type="password" autocomplete="new-password" required>
                            </div>

                            <div class="field">
                                <label for="register-password-confirmation">Confirmar senha</label>
                                <input class="input" id="register-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>

                            <button class="button" type="submit">Cadastrar</button>
                        </form>
                    </div>
                </section>

                <section class="grid dashboard-grid hidden" id="wallet-view">
                    <div class="grid">
                        <div class="card">
                            <span class="muted" id="current-user">Usuário autenticado</span>
                            <div class="balance" id="wallet-balance">R$ 0,00</div>
                        </div>

                        <div class="card">
                            <h2>Depositar</h2>
                            <form class="form" id="deposit-form">
                                <div class="field">
                                    <label for="deposit-amount">Valor</label>
                                    <input class="input" id="deposit-amount" name="amount" type="number" min="0.01" step="0.01" placeholder="100.00" required>
                                </div>

                                <div class="field">
                                    <label for="deposit-description">Descrição</label>
                                    <textarea class="input" id="deposit-description" name="description" maxlength="255"></textarea>
                                </div>

                                <button class="button" type="submit">Depositar</button>
                            </form>
                        </div>

                        <div class="card">
                            <h2>Transferir</h2>
                            <form class="form" id="transfer-form">
                                <div class="field">
                                    <label for="recipient-id">Destinatário</label>
                                    <select class="input" id="recipient-id" name="recipient_id" required></select>
                                </div>

                                <div class="field">
                                    <label for="transfer-amount">Valor</label>
                                    <input class="input" id="transfer-amount" name="amount" type="number" min="0.01" step="0.01" placeholder="25.00" required>
                                </div>

                                <div class="field">
                                    <label for="transfer-description">Descrição</label>
                                    <textarea class="input" id="transfer-description" name="description" maxlength="255"></textarea>
                                </div>

                                <button class="button" type="submit">Transferir</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <h2>Transações</h2>
                        <div class="transaction-list" id="transactions"></div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
