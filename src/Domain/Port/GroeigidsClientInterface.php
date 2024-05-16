<?php

namespace Endeavour\GroeigidsApiClient\Domain\Port;

use DateTimeInterface;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Query\SortParameter;

interface GroeigidsClientInterface
{
    /**
     * @param TypedArray<SortParameter>|null $sortParameters
     * @return TypedArray<Article>
     */
    public function fetchArticles(
        int $page = 0,
        int $size = 20,
        ?TypedArray $sortParameters = null,
    ): TypedArray;

    public function fetchArticle(int $id, bool $includeChildren = false): Article;

    /**
     * @param TypedArray<SortParameter>|null $sortParameters
     * @return TypedArray<Article>
     */
    public function fetchModfifiedArticlesAfterDate(
        DateTimeInterface $modifiedDate,
        int $page = 0,
        int $size = 20,
        ?TypedArray $sortParameters = null,
    ): TypedArray;

    /**
     * @return TypedArray<Article>
     */
    public function fetchThemeArticles(bool $includeChildren = false): TypedArray;

    public function fetchArticleByBreadcrumb(string $breadcrumb): Article;
}