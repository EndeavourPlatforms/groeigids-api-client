<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

class SortObject
{
    public function __construct(
        public readonly bool $sorted,
        public readonly bool $empty,
        public readonly bool $unsorted,
    ) {
    }
}