<?php

namespace KDuma\emSzmalAPI\DTO;

use GuzzleHttp\Cookie\CookieJar;

class Session
{
    public function __construct(
        public readonly string $id,
    ) { }

    public function __toString(): string
    {
        return $this->id;
    }

    public function toCookieJar(): CookieJar
    {
        return CookieJar::fromArray([
            'SessionId' => $this->id,
        ], 'web.emszmal.pl');
    }
}