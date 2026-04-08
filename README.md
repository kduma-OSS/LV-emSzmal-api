# emSzmal Banking API Wrapper

[![Latest Stable Version](https://poser.pugx.org/kduma/emszmal-api/v/stable.svg)](https://packagist.org/packages/kduma/emszmal-api)
[![Total Downloads](https://poser.pugx.org/kduma/emszmal-api/downloads.svg)](https://packagist.org/packages/kduma/emszmal-api)
[![License](https://poser.pugx.org/kduma/emszmal-api/license.svg)](https://packagist.org/packages/kduma/emszmal-api)

PHP wrapper for the [emSzmal](https://emszmal.pl) banking API — enables fetching account data and transaction history from Polish banks.

Full documentation: [opensource.duma.sh/libraries/php/emszmal](https://opensource.duma.sh/libraries/php/emszmal)

## Requirements

- PHP `^8.3`
- Laravel `^12.0 || ^13.0` (optional — also works as plain PHP)

## Installation

```bash
composer require kduma/emszmal-api
```

## Usage

```php
$api = new \KDuma\emSzmalAPI\emSzmalAPI(
    api_id: $api_id,
    api_key: $api_key,
    cache_provider: new \KDuma\emSzmalAPI\CacheProviders\NoCacheProvider(),
);

$session = $api->SayHello();

$accounts = $api->GetAccountsList(
    session: $session,
    credentials: new \KDuma\emSzmalAPI\DTO\BankCredentials(
        provider: \KDuma\emSzmalAPI\Enums\Bank::PKOiPKO,
        login: 'login',
        password: 'password',
        user_context: '',
        token_value: '',
    ),
);

$api->SayBye(session: $session);
```

In Laravel, you can resolve the client from the container after adding credentials to `.env`:

```php
$api = app(\KDuma\emSzmalAPI\emSzmalAPI::class);
```
