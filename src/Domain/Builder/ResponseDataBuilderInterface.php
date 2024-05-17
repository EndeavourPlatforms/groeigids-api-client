<?php

namespace Endeavour\GroeigidsApiClient\Domain\Builder;

use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageableObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageArticle;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\SortObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\Themes;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Model\ArticleChild;

interface ResponseDataBuilderInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function buildPageArticle(array $data): PageArticle;

    /**
     * @param array<string, mixed> $data
     */
    public function buildArticle(array $data): Article;

    /**
     * @param array<string, mixed> $data
     */
    public function buildArticleChild(array $data): ArticleChild;

    /**
     * @param array<string, mixed> $data
     */
    public function buildPageableObject(array $data): PageableObject;

    /**
     * @param array<string, mixed> $data
     */
    public function buildSortObject(array $data): SortObject;

    /**
     * @param array<string, mixed> $data
     */
    public function buildThemes(array $data): Themes;
}