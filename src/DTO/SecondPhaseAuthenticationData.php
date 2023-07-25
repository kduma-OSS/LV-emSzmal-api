<?php

namespace KDuma\emSzmalAPI\DTO;

class SecondPhaseAuthenticationData
{
    public function __construct(
        public readonly string $AuthenticationMethod,
        public readonly string $PreAuthenticationCode,
        public readonly string $AuthenticationCodeHint,
    ) { }
}