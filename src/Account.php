<?php


    namespace KDuma\emSzmalAPI;


    /**
     * Class Account
     *
     * @package KDuma\emSzmalAPI
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
         * @var double
         */
        public $available_funds;

        /**
         * @var double
         */
        public $balance;

        /**
         * @param string $number
         *
         * @return Account
         */
        public function setNumber ($number)
        {
            $this->number = $number;

            return $this;
        }

        /**
         * @param string $currency
         *
         * @return Account
         */
        public function setCurrency ($currency)
        {
            $this->currency = $currency;

            return $this;
        }

        /**
         * @param double $available_funds
         *
         * @return Account
         */
        public function setAvailableFunds ($available_funds)
        {
            $this->available_funds = $available_funds;

            return $this;
        }

        /**
         * @param double $balance
         *
         * @return Account
         */
        public function setBalance ($balance)
        {
            $this->balance = $balance;

            return $this;
        }
        /**
         * @return string
         */
        public function getNumber ()
        {
            return $this->number;
        }

        /**
         * @return string
         */
        public function getCurrency ()
        {
            return $this->currency;
        }

        /**
         * @return double
         */
        public function getAvailableFunds ()
        {
            return $this->available_funds;
        }

        /**
         * @return double
         */
        public function getBalance ()
        {
            return $this->balance;
        }

        /**
         * Account constructor.
         *
         * @param string $number
         * @param string $currency
         * @param double $available_funds
         * @param double $balance
         */
        public function __construct ($number, $currency, $available_funds, $balance)
        {
            $this->number = $number;
            $this->currency = $currency;
            $this->available_funds = $available_funds;
            $this->balance = $balance;
        }
    }