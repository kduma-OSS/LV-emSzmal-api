<?php

namespace KDuma\emSzmalAPI;

/**
 * Class BankCredentials
 *
 * @package KDuma\emSzmalAPI
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
     * BankCredentials constructor.
     *
     * @param int    $provider
     * @param string $login
     * @param string $password
     * @param string $user_context
     */
    public function __construct($provider, $login, $password, $user_context = 'I')
    {
        $this->provider = (int) $provider;
        $this->login = $login;
        $this->password = $password;
        $this->user_context = $user_context;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ProviderId' => $this->provider,
            'Authentication' => [
                'UserLogin' => $this->login,
                'UserPassword' => $this->password,
                'UserContext' => $this->user_context,
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
