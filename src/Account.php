<?php

namespace KDuma\emSzmalAPI;

/**
     * Class Account.
     */
    class Account
    {
        /**
         * @var string
         */
        public $number;

        /**
         * @var string
         */
        public $currency;

        /**
         * @var float
         */
        public $available_funds;

        /**
         * @var float
         */
        public $balance;

        /**
         * @param string $number
         *
         * @return Account
         */
        public function setNumber($number)
        {
            $this->number = $number;

            return $this;
        }

        /**
         * @param string $currency
         *
         * @return Account
         */
        public function setCurrency($currency)
        {
            $this->currency = $currency;

            return $this;
        }

        /**
         * @param float $available_funds
         *
         * @return Account
         */
        public function setAvailableFunds($available_funds)
        {
            $this->available_funds = $available_funds;

            return $this;
        }

        /**
         * @param float $balance
         *
         * @return Account
         */
        public function setBalance($balance)
        {
            $this->balance = $balance;

            return $this;
        }

        /**
         * @return string
         */
        public function getNumber()
        {
            return $this->number;
        }

        /**
         * @return string
         */
        public function getCurrency()
        {
            return $this->currency;
        }

        /**
         * @return float
         */
        public function getAvailableFunds()
        {
            return $this->available_funds;
        }

        /**
         * @return float
         */
        public function getBalance()
        {
            return $this->balance;
        }

        /**
         * Account constructor.
         *
         * @param string $number
         * @param string $currency
         * @param float $available_funds
         * @param float $balance
         */
        public function __construct($number, $currency, $available_funds, $balance)
        {
            $this->number = $number;
            $this->currency = $currency;
            $this->available_funds = $available_funds;
            $this->balance = $balance;
        }
    }
