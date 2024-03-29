<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;

class PageArticle
{
    /**
     * @param TypedArray<Article> $articles
     */
    public function __construct(
        public readonly TypedArray $articles,
        public readonly int $number,
        public readonly int $totalPages,
        public readonly int $size,
        public readonly int $totalElements,
        public readonly PageableObject $pageable,
        public readonly SortObject $sort,
        public readonly int $numberOfElements,
        public readonly bool $last,
        public readonly bool $first,
        public readonly bool $empty,
    ) {
    }
}