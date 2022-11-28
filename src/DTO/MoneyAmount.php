<?php

namespace KDuma\emSzmalAPI\DTO;

class MoneyAmount
{
    public function __construct(
        public readonly int $amount,
        public readonly int $decimals = 2,
    ) { }
    
    public static function fromFloat(float $amount, int $decimals = 2): static
    {
        return new static((int) ($amount * 10 ** $decimals), $decimals);
    }
    
    public function __toString(): string
    {
        return number_format($this->toFloat(), $this->decimals, '.', '');
    }
    
    public function toFloat(): float
    {
        return $this->amount / 10 ** $this->decimals;
    }
}