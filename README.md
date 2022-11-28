# emSzmal Banking API wrapper in PHP

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI](https://styleci.io/repos/72450991/shield?branch=master)](https://styleci.io/repos/72450991)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/97e54d0d-ad26-41d7-9956-843893a5d897/mini.png)](https://insight.sensiolabs.com/projects/97e54d0d-ad26-41d7-9956-843893a5d897)

emSzmal Banking API wrapper in PHP

## Install

Via Composer

``` bash
$ composer require kduma/emszmal-api
```

## Usage

``` php
$api = new \KDuma\emSzmalAPI\emSzmalAPI($api_id, $api_key);

$BankCredentials = new BankCredentials(\KDuma\emSzmalAPI\Bank::PKOiPKO, "Login", "Password");

$accounts = $api->GetAccountsList($BankCredentials);
$transactions = $api->GetAccountHistory("account number", '2016-10-25', '2016-10-30', $BankCredentials);
```

## Laravel Usage

### Setup

Then add the Service Provider to the providers array in `config/app.php`:

    KDuma\emSzmalAPI\Laravel\ServiceProvider::class,

Add following entries to your `.env` file:

	EMSZMAL_API_ID="<api id>"
	EMSZMAL_API_KEY=<api key>

	EMSZMAL_BANK_PROVIDER_ID=<provider ID>
	EMSZMAL_BANK_LOGIN=<login>
	EMSZMAL_BANK_PASSWORD=<password>
    EMSZMAL_BANK_USER_TOKEN=<token_from_bank>
    
### Usage
You can resolve `emSzmalAPI::class` class:
``` php
use KDuma\emSzmalAPI\emSzmalAPI;

$api = app(emSzmalAPI::class);

$accounts = $api->GetAccountsList();
$transactions = $api->GetAccountHistory("account number", '2016-10-25', '2016-10-30');
```
or You can use injection container
``` php
use KDuma\emSzmalAPI\emSzmalAPI;

Route::get('/api', function (emSzmalAPI $api) {
    $accounts = $api->GetAccountsList();
    $transactions = $api->GetAccountHistory("account number", '2016-10-25', '2016-10-30');
});
```
    
### Multiple Bank Credentials

You can use multiple bank credentials. First, run the following command to copy config file:

    php artisan vendor:publish --provider="KDuma\emSzmalAPI\Laravel\ServiceProvider"

In Your `config/emszmalapi.php` file, in `bank_credentials` section add additional credentials:

``` php
'bank_credentials' => [
    'bank_1' => [
        'provider' => env('EMSZMAL_BANK_1_PROVIDER_ID'),
        'login' => env('EMSZMAL_BANK_1_LOGIN'),
        'password' => env('EMSZMAL_BANK_1_PASSWORD'),
        'user_context' => env('EMSZMAL_BANK_1_USER_CONTEXT', "I"),
    ],
    'bank_2' => [
        'provider' => env('EMSZMAL_BANK_2_PROVIDER_ID'),
        'login' => env('EMSZMAL_BANK_2_LOGIN'),
        'password' => env('EMSZMAL_BANK_2_PASSWORD'),
        'user_context' => env('EMSZMAL_BANK_2_USER_CONTEXT', "I"),
    ],
],
```
Now you can use the alias when calling API methods:
``` php
use KDuma\emSzmalAPI\emSzmalAPI;

$api = app(emSzmalAPI::class);

$bank_1_accounts = $api->GetAccountsList('bank_1');
$bank_1_transactions = $api->GetAccountHistory("account number", '2016-10-25', '2016-10-30', 'bank_1');

$bank_2_accounts = $api->GetAccountsList('bank_2');
$bank_2_transactions = $api->GetAccountHistory("account number", '2016-10-25', '2016-10-30', 'bank_2');
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
