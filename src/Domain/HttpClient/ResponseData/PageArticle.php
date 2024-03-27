<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;

class PageArticle
{
    public readonly PageableObject $pageable;

    /**
     * @var TypedArray<Article> $articles
     */
    public readonly TypedArray $articles;
    public readonly SortObject $sort;

    /**
     * @param array<string, int|string|boolean|array<string, mixed>> $content
     * @param array<string, mixed> $pageable
     * @param array<string, mixed> $sort
     */
    public function __construct(
        array $content,
        public readonly int $number,
        public readonly int $totalPages,
        public readonly int $size,
        public readonly int $totalElements,
        array $pageable,
        array $sort,
        public readonly int $numberOfElements,
        public readonly bool $last,
        public readonly bool $first,
        public readonly bool $empty,
    ) {
        $articles = array_map(fn(array $articleArray) => new Article(...$articleArray), $content);

        $this->articles = new TypedArray(Article::class, $articles);
        $this->pageable = new PageableObject(...$pageable);
        $this->sort = new SortObject(...$sort);
    }
}