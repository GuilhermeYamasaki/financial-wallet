# Financial Wallet

Interface funcional equivalente a uma carteira financeira em que usuários podem criar conta, autenticar, depositar dinheiro, transferir saldo, receber transferências, consultar histórico e reverter operações.

## Pré-requisitos

- [Git](https://git-scm.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Composer](https://getcomposer.org/)

`Certifique-se de que você tenha todas as dependências acima instaladas antes de prosseguir.`

## Passos

#### Clonar o repositório

```bash
git clone https://github.com/GuilhermeYamasaki/financial-wallet.git
```

#### Entrar na pasta

```bash
cd financial-wallet
```

#### Baixar dependências

```bash
composer install
```

#### Copiar .env

```bash
cp .env.example .env
```

#### Adicionar alias do Sail

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

#### Construir container

```bash
sail up -d
```

#### Gerar chave criptografada

```bash
sail artisan key:generate
```

#### Criar banco de dados

```bash
sail artisan migrate:fresh
```

#### Rodar os testes

```bash
sail artisan test
```

#### Abrir terminal e deixar executando

```bash
sail artisan queue:work
```

#### Acessar a aplicação

```bash
http://localhost
```

## Tecnologias

- [Laravel 13.x](https://laravel.com/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Pint](https://laravel.com/docs/pint)
- [Redis](https://redis.io/)
- [PostgreSQL](https://www.postgresql.org/)
- [Telescope](https://laravel.com/docs/telescope)
- [Horizon](https://laravel.com/docs/horizon)
- [Sail](https://laravel.com/docs/sail)
- [Docker](https://www.docker.com/)
