<?php

namespace KDuma\emSzmalAPI;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use KDuma\emSzmalAPI\CacheProviders\CacheProviderInterface;
use KDuma\emSzmalAPI\CacheProviders\NoCacheProvider;
use KDuma\emSzmalAPI\DTO\Account;
use KDuma\emSzmalAPI\DTO\BankCredentials;
use KDuma\emSzmalAPI\Values\Money;
use KDuma\emSzmalAPI\DTO\Transaction;

/**
 * Class emSzmalAPI.
 */
class emSzmalAPI
{
    protected readonly Client $client;
    private ?string $session_id;

    /**
     * @var callable|null
     */
    protected $default_bank_credentials_resolver = null;

    public function __construct(
        public readonly string $api_id,
        public readonly string $api_key,
        public readonly int $timeout = 120,
        public readonly CacheProviderInterface $cache_provider = new NoCacheProvider(),
    )
    {
        $this->client = new Client([
            'base_uri' => 'https://web.emszmal.pl/',
            'timeout'  => $timeout,
            'cookies' => true,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function __destruct()
    {
        $this->SayBye();
    }

    /**
     * @throws GuzzleException
     */
    public function SayHello(): string
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
     * @return Account[]
     * @throws Exception|GuzzleException
     */
    public function GetAccountsList(string|BankCredentials $credentials = null): array
    {
        $credentials = $this->GetCredentials($credentials);

        $cache_key = 'GetAccountsList.'.$credentials->provider.'.'.$credentials->login;
        $data = $this->cache_provider->cache($cache_key, function () use ($credentials) {
            if (!$this->session_id) {
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
                Money::fromFloat($account['AccountAvailableFunds']),
                Money::fromFloat($account['AccountBalance'])
            );
        }

        return $accounts;
    }

    /**
     * @return Transaction[]
     * @throws Exception|GuzzleException
     */
    public function GetAccountHistory(
        string $account_number, 
        DateTimeImmutable|string $date_since, 
        DateTimeImmutable|string $date_to, 
        string|BankCredentials $credentials = null
    ): array
    {
        $credentials = $this->GetCredentials($credentials);

        if (! $date_since instanceof DateTimeImmutable) {
            $date_since = new DateTimeImmutable($date_since);
        }

        if (! $date_to instanceof DateTimeImmutable) {
            $date_to = new DateTimeImmutable($date_to);
        }

        $cache_key = 'GetAccountHistory.'.$credentials->provider.'.'.$credentials->login.'.'.$account_number.'.'.$date_since->format('Y-m-d').'.'.$date_to->format('Y-m-d');
        $data = $this->cache_provider->cache($cache_key, function () use ($account_number, $date_since, $date_to, $credentials) {
            if (!$this->session_id) {
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
                new DateTimeImmutable($transaction['TransactionOperationDate']),
                new DateTimeImmutable($transaction['TransactionBookingDate']),
                Money::fromFloat($transaction['TransactionAmount']),
                Money::fromFloat($transaction['TransactionBalance']),
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
     * @throws GuzzleException
     */
    public function SayBye(): bool
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

    public function setDefaultBankCredentialsResolver(callable $default_bank_credentials_resolver = null): static
    {
        $this->default_bank_credentials_resolver = $default_bank_credentials_resolver;

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function GetCredentials(string|BankCredentials $credentials = null): BankCredentials
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
