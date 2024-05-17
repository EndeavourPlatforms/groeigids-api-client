<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

class PageableObject
{
    public function __construct(
        public readonly int $pageNumber,
        public readonly int $pageSize,
        public readonly int $offset,
        public readonly SortObject $sort,
        public readonly bool $paged,
        public readonly bool $unpaged,
    ) {
    }
}
