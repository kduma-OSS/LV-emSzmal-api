# emSzmal Banking API wrapper in PHP

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

emSzmal Banking API wrapper in PHP

## Install

Via Composer

``` bash
$ composer require kduma/emszmal-api
```

## Usage

```php
$api = new \KDuma\emSzmalAPI\emSzmalAPI(
    api_id: $api_id, 
    api_key: $api_key,
    timeout: 120,
    cache_provider: new \KDuma\emSzmalAPI\CacheProviders\NoCacheProvider(),
);

$BankCredentials = new \KDuma\emSzmalAPI\DTO\BankCredentials(
    provider: \KDuma\emSzmalAPI\Enums\Bank::PKOiPKO, 
    login: 'Login', 
    password: 'Password',
    user_context: '',
    token_value: '',
);

$accounts = $api->GetAccountsList(
    credentials: $BankCredentials,
);

$transactions = $api->GetAccountHistory(
    account_number: "account number", 
    date_since: '2016-10-25', 
    date_to: '2016-10-30', 
    credentials: $BankCredentials,
);
```

## Laravel Usage

### Setup

Add following entries to your `.env` file:

	EMSZMAL_API_ID="<api id>"
	EMSZMAL_API_KEY=<api key>

	EMSZMAL_BANK_PROVIDER_ID=<provider ID>
	EMSZMAL_BANK_LOGIN=<login>
	EMSZMAL_BANK_PASSWORD=<password>
    EMSZMAL_BANK_USER_TOKEN=<token from bank>
    
### Usage
You can resolve `emSzmalAPI::class` class:

```php
$api = app(\KDuma\emSzmalAPI\emSzmalAPI::class);

$accounts = $api->GetAccountsList();

$transactions = $api->GetAccountHistory(
    account_number: 'account number', 
    date_since: '2016-10-25', 
    date_to: '2016-10-30',
);
```

or You can use injection container

```php
Route::get('/api', function (\KDuma\emSzmalAPI\emSzmalAPI $api) {
    $accounts = $api->GetAccountsList();
    
    $transactions = $api->GetAccountHistory(
        account_number: 'account number', 
        date_since: '2016-10-25', 
        date_to: '2016-10-30',
    );
});
```
    
### Multiple Bank Credentials

You can use multiple bank credentials. First, run the following command to copy config file:

    php artisan vendor:publish --provider="KDuma\emSzmalAPI\Laravel\ServiceProvider"

In Your `config/emszmalapi.php` file, in `bank_credentials` section add additional credentials:

```php
'bank_credentials' => [
    'bank_1' => [
        'provider' => env('EMSZMAL_BANK_1_PROVIDER_ID'),
        'login' => env('EMSZMAL_BANK_1_LOGIN'),
        'password' => env('EMSZMAL_BANK_1_PASSWORD'),
        'user_context' => env('EMSZMAL_BANK_1_USER_CONTEXT', "I"),
        'token_value' => env('EMSZMAL_BANK_1_USER_TOKEN', ''),
    ],
    'bank_2' => [
        'provider' => env('EMSZMAL_BANK_2_PROVIDER_ID'),
        'login' => env('EMSZMAL_BANK_2_LOGIN'),
        'password' => env('EMSZMAL_BANK_2_PASSWORD'),
        'user_context' => env('EMSZMAL_BANK_2_USER_CONTEXT', "I"),
        'token_value' => env('EMSZMAL_BANK_2_USER_TOKEN', ''),
    ],
],
```
Now you can use the alias when calling API methods:
```php
$api = app(\KDuma\emSzmalAPI\emSzmalAPI::class);

$bank_1_accounts = $api->GetAccountsList(
    credentials: 'bank_1',
);
$bank_1_transactions = $api->GetAccountHistory(
    account_number: "account number", 
    date_since: '2016-10-25', 
    date_to: '2016-10-30', 
    credentials: 'bank_1',
);


$bank_2_accounts = $api->GetAccountsList(
    credentials: 'bank_2',
);

$bank_2_transactions = $api->GetAccountHistory(
    account_number: "account number", 
    date_since: '2016-10-25', 
    date_to: '2016-10-30', 
    credentials: 'bank_2',
);
```

## Credits

- [Krystian Duma][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kduma/emszmal-api.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kduma/emszmal-api.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kduma/emszmal-api
[link-downloads]: https://packagist.org/packages/kduma/emszmal-api
[link-author]: https://github.com/kduma
[link-contributors]: ../../contributors
