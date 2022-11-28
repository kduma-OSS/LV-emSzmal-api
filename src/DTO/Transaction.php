<?php

namespace KDuma\emSzmalAPI\DTO;

use DateTimeImmutable;


class Transaction
{
    public function __construct(
        public readonly string            $reference_number,
        public readonly DateTimeImmutable $operation_date,
        public readonly DateTimeImmutable $booking_date,
        public readonly MoneyAmount       $amount,
        public readonly MoneyAmount       $balance,
        public readonly string            $type,
        public readonly string            $description,
        public readonly string            $partner_name,
        public readonly string            $partner_account,
        public readonly string            $payment_details,
    ) { }
}
