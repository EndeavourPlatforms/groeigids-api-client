<?php

namespace Endeavour\GroeigidsApiClient\Domain\Port;

use DateTimeInterface;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;

interface GroeigidsClientInterface
{
    /**
     * @param array<string>|null $sort
     * @example ['orderId,desc', 'some_other_property,asc']
     * @return TypedArray<Article>
     */
    public function fetchArticles(
        int $page = 0,
        int $size = 20,
        array $sort = null,
    ): TypedArray;

    public function fetchArticle(int $id, bool $includeChildren = false): Article;

    /**
     * @param array{string: 'asc'|'desc'}|null $sort
     * @example ['orderId,desc', 'some_other_property,asc']
     * @return TypedArray<Article>
     */
    public function fetchModfifiedArticlesAfterDate(
        DateTimeInterface $dateTime,
        int $page = 0,
        int $size = 20,
        array $sort = null,
    ): TypedArray;

    /**
     * @return TypedArray<Article>
     */
    public function fetchThemeArticles(bool $withChildren = false): TypedArray;

    public function fetchArticleByBreadcrumb(string $breadcrumb): Article;
}