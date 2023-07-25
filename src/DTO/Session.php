<?php

namespace KDuma\emSzmalAPI\DTO;

class Session
{
    public function __construct(
        public readonly string $id,
    ) { }

    public function __toString(): string
    {
        return $this->id;
    }
}