<?php

namespace KDuma\emSzmalAPI;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use KDuma\emSzmalAPI\CacheProviders\CacheProviderInterface;

/**
 * Class emSzmalAPI.
 */
class emSzmalAPI
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    private $api_id;

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string|null
     */
    private $session_id;

    /**
     * @var CacheProviderInterface|null
     */
    protected $cache_provider = null;

    /**
     * @var callable|null
     */
    protected $default_bank_credentials_resolver = null;

    /**
     * emSzmalAPI constructor.
     *
     * @param string $api_id
     * @param string $api_key
     * @param int $timeout
     */
    public function __construct($api_id, $api_key, $timeout = 120)
    {
        $this->api_id = $api_id;
        $this->api_key = $api_key;
        $this->client = new Client([
            'base_uri' => 'https://web.emszmal.pl/',
            'timeout'  => $timeout,
            'cookies' => true,
        ]);
    }

    /**
     * emSzmalAPI destructor.
     */
    public function __destruct()
    {
        $this->SayBye();
    }

    /**
     * @return string
     */
    public function SayHello()
    {
        if ($this->session_id) {
            return $this->session_id;
        }

        $response = $this->client->post('/api/Common/SayHello', [
            'json' => [
                'License' => [
                    'APIId' => $this->api_id,
                    'APIKey' => $this->api_key,
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->session_id = $data['SessionId'];
    }

    /**
     * @param BankCredentials|string|null $credentials
     *
     * @return Account[]
     */
    public function GetAccountsList($credentials = null)
    {
        $credentials = $this->GetCredentials($credentials);

        $cache_key = 'GetAccountsList.'.$credentials->getProvider().'.'.$credentials->getLogin();
        $data = $this->cache($cache_key, function () use ($credentials) {
            if (! $this->session_id) {
                $this->SayHello();
            }

            $response = $this->client->post('/api/Accounts/GetAccountsList', [
                'json' => $credentials->toArray() + [
                    'SessionId' => $this->session_id,
                    'License' => [
                        'APIId' => $this->api_id,
                        'APIKey' => $this->api_key,
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        });

        $accounts = [];

        foreach ($data['Accounts'] as $account) {
            $accounts[] = new Account(
                $account['AccountNumber'],
                $account['AccountCurrency'],
                $account['AccountAvailableFunds'],
                $account['AccountBalance']
            );
        }

        return $accounts;
    }

    /**
     * @param string                      $account_number
     * @param DateTime|string             $date_since
     * @param DateTime|string             $date_to
     * @param BankCredentials|string|null $credentials
     *
     * @return Transaction[]
     */
    public function GetAccountHistory($account_number, $date_since, $date_to, $credentials = null)
    {
        $credentials = $this->GetCredentials($credentials);

        if (! $date_since instanceof DateTime) {
            $date_since = new DateTime($date_since);
        }

        if (! $date_to instanceof DateTime) {
            $date_to = new DateTime($date_to);
        }

        $cache_key = 'GetAccountHistory.'.$credentials->getProvider().'.'.$credentials->getLogin().'.'.$account_number.'.'.$date_since->format('Y-m-d').'.'.$date_to->format('Y-m-d');
        $data = $this->cache($cache_key, function () use ($account_number, $date_since, $date_to, $credentials) {
            if (! $this->session_id) {
                $this->SayHello();
            }

            $response = $this->client->post('/api/Accounts/GetAccountHistory', [
                'json' => $credentials->toArray() + [
                    'SessionId' => $this->session_id,
                    'Data' => [
                        'AccountNumber' => $account_number,
                        'DateSince' => $date_since->format('Y-m-d'),
                        'DateTo' => $date_to->format('Y-m-d'),
                    ],
                    'License' => [
                        'APIId' => $this->api_id,
                        'APIKey' => $this->api_key,
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        });

        $transactions = [];

        foreach ($data['Transactions'] as $transaction) {
            $transactions[] = new Transaction(
                $transaction['TransactionRefNumber'],
                new DateTime($transaction['TransactionOperationDate']),
                new DateTime($transaction['TransactionBookingDate']),
                $transaction['TransactionAmount'],
                $transaction['TransactionBalance'],
                $transaction['TransactionType'],
                $transaction['TransactionDescription'],
                $transaction['TransactionPartnerName'],
                $transaction['TransactionPartnerAccountNo'],
                $transaction['TransactionPaymentDetails']
            );
        }

        return $transactions;
    }

    /**
     * @return bool
     */
    public function SayBye()
    {
        if (! $this->session_id) {
            return false;
        }

        $this->client->post('/api/Common/SayBye', [
            'json' => [
                'SessionId' => $this->session_id,
                'License' => [
                    'APIId' => $this->api_id,
                    'APIKey' => $this->api_key,
                ],
            ],
        ]);

        $this->session_id = null;

        return true;
    }

    /**
     * @param string   $cache_key
     * @param callable $callable
     *
     * @return array
     */
    private function cache($cache_key, callable $callable)
    {
        if (! $this->cache_provider) {
            return $callable();
        }

        return $this->cache_provider->cache($cache_key, $callable);
    }

    /**
     * @param CacheProviderInterface|null $cache_provider
     *
     * @return emSzmalAPI
     */
    public function setCacheProvider(CacheProviderInterface $cache_provider = null)
    {
        $this->cache_provider = $cache_provider;

        return $this;
    }

    /**
     * @param callable|null $default_bank_credentials_resolver
     *
     * @return emSzmalAPI
     */
    public function setDefaultBankCredentialsResolver(callable $default_bank_credentials_resolver = null)
    {
        $this->default_bank_credentials_resolver = $default_bank_credentials_resolver;

        return $this;
    }

    /**
     * @param BankCredentials|string|null $credentials
     *
     * @return BankCredentials
     * @throws Exception
     */
    protected function GetCredentials($credentials = null)
    {
        if ($credentials instanceof BankCredentials) {
            return $credentials;
        }

        if ($this->default_bank_credentials_resolver) {
            $resolver = $this->default_bank_credentials_resolver;

            return is_null($credentials) ? $resolver() : $resolver($credentials);
        }

        throw new Exception('Missing BankCredentials');
    }
}
