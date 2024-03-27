<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

class PageableObject
{
    public readonly SortObject $sort;

    /**
     * @param array<string, boolean> $sort
     */
    public function __construct(
        public readonly int $pageNumber,
        public readonly int $pageSize,
        public readonly int $offset,
        array $sort,
        public readonly bool $paged,
        public readonly bool $unpaged,
    ) {
        $this->sort = new SortObject(...$sort);
    }
}