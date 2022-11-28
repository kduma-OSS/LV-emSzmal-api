<?php

namespace KDuma\emSzmalAPI\DTO;

use KDuma\emSzmalAPI\Enums\Bank;

class BankCredentials
{
    public readonly int $provider;

    public function __construct(
        int|Bank $provider, 
        public readonly string $login, 
        public readonly string $password, 
        public readonly string $user_context = '', 
        public readonly string $token_value = ''
    ){
        $this->provider = (int) $provider;
    }
    
    public function toArray(): array
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
}
