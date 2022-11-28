<?php

namespace KDuma\emSzmalAPI\DTO;

class Account
{
    public function __construct(
        public readonly string      $number, 
        public readonly string      $currency, 
        public readonly MoneyAmount $available_funds, 
        public readonly MoneyAmount $balance,
    ) { }
}
