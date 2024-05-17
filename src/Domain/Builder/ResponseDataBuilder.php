<?php

namespace Endeavour\GroeigidsApiClient\Domain\Builder;

use DateTimeImmutable;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageableObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageArticle;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\SortObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\Themes;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Model\ArticleChild;
use Throwable;


class ResponseDataBuilder implements ResponseDataBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildPageArticle(array $data): PageArticle
    {
        $articles = new TypedArray(
            Article::class,
            array_map(fn(array $articleArray) => $this->buildArticle($articleArray), $data['content'])
        );

        $pageable = $this->buildPageableObject($data['pageable']);
        $sort = $this->buildSortObject($data['sort']);

        return new PageArticle(
            $articles,
            $data['number'],
            $data['totalPages'],
            $data['size'],
            $data['totalElements'],
            $pageable,
            $sort,
            $data['numberOfElements'],
            $data['last'],
            $data['first'],
            $data['empty']
        );
    }

    /**
     * @inheritDoc
     */
    public function buildArticle(array $data): Article
    {
        $children = new TypedArray(
            ArticleChild::class,
            array_map(fn(array $childArray) => $this->buildArticleChild($childArray), $data['children'] ?? [])
        );

        try {
            $modified = new DateTimeImmutable($data['modified']);
        } catch (Throwable) {
            $modified = null;
        }

        return new Article(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['breadcrumb'],
            $data['media'],
            $data['mediaExtra'],
            $data['hasContent'],
            $data['canonical'],
            $data['orderId'],
            $children,
            $data['parentId'] ?? null,
            $modified
        );
    }

    /**
     * @inheritDoc
     */
    public function buildArticleChild(array $data): ArticleChild
    {
        $children = new TypedArray(
            ArticleChild::class,
            array_map(fn(array $childArray) => $this->buildArticleChild($childArray), $data['children'] ?? [])
        );

        return new ArticleChild(
            id: $data['id'],
            title: $data['title'],
            breadcrumb: $data['breadcrumb'],
            hasContent: $data['hasContent'],
            children: $children,
        );
    }

    /**
     * @inheritDoc
     */
    public function buildPageableObject(array $data): PageableObject
    {
        $sort = $this->buildSortObject($data['sort']);

        return new PageableObject(
            pageNumber: $data['pageNumber'],
            pageSize: $data['pageSize'],
            offset: $data['offset'],
            sort: $sort,
            paged: $data['paged'],
            unpaged: $data['unpaged']
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function buildSortObject(array $data): SortObject
    {
        return new SortObject(
            $data['sorted'],
            $data['empty'],
            $data['unsorted']
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function buildThemes(array $data): Themes
    {
        $themes = new TypedArray(
            Article::class,
            array_map(fn(array $articleArray) => $this->buildArticle($articleArray), $data)
        );

        return new Themes($themes);
    }
}