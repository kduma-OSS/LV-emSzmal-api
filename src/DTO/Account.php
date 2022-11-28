<?php

namespace KDuma\emSzmalAPI\DTO;

use KDuma\emSzmalAPI\Values\Money;

class Account
{
    public function __construct(
        public readonly string $number, 
        public readonly string $currency, 
        public readonly Money  $available_funds, 
        public readonly Money  $balance,
    ) { }
}
