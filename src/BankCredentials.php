<?php

namespace KDuma\emSzmalAPI;

/**
 * Class BankCredentials.
 */
class BankCredentials
{
    /**
     * @var int
     */
    protected $provider;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $user_context;

    /**
     * @var string
     */
    protected $token_value;

    /**
     * BankCredentials constructor.
     *
     * @param int    $provider
     * @param string $login
     * @param string $password
     * @param string $user_context
     * @param string $token_value
     */
    public function __construct($provider, $login, $password, $user_context = '', $token_value = '')
    {
        $this->provider = (int) $provider;
        $this->login = $login;
        $this->password = $password;
        $this->user_context = $user_context;
        $this->token_value = $token_value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ProviderId' => $this->provider,
            'ProviderConfiguration' => '',
            'Authentication' => [
                'UserLogin' => $this->login,
                'UserPassword' => $this->password,
                'UserContext' => $this->user_context,
                'TokenValue' => $this->token_value,
            ],
        ];
    }

    /**
     * @return int
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
}
