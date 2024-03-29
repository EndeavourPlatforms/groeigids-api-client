<?php

namespace Endeavour\GroeigidsApiClient\Test\Unit\Domain\Collection;

use DateTime;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidTypedArgumentException;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Model\ArticleChild;
use PHPUnit\Framework\TestCase;

class TypedArrayTest extends TestCase
{
    public function testCreateArticleTypedArray(): void
    {
        $articleArray = new TypedArray(Article::class);

        $this->assertEquals(Article::class, $articleArray->type);
    }

    public function testAddArticleToArticleTypedArray(): void
    {
        $articleArray = new TypedArray(Article::class);

        $article = new Article(
            id: 0,
            title: '',
            content: '',
            breadcrumb: '',
            media: '',
            mediaExtra: '',
            hasContent: true,
            canonical: '',
            orderId: 0,
            children: new TypedArray(ArticleChild::class),
            parentId: 0,
            modified: new \DateTimeImmutable(),
        );
        $articleArray[] = $article;

        $this->assertCount(1, $articleArray);
    }

    public function testAddWrongObjectToTypedArray(): void
    {
        $this->expectException(InvalidTypedArgumentException::class);

        $articleArray = new TypedArray(ArticleChild::class);

        $article = new Article(
            id: 0,
            title: '',
            content: '',
            breadcrumb: '',
            media: '',
            mediaExtra: '',
            hasContent: true,
            canonical: '',
            orderId: 0,
            children: new TypedArray(ArticleChild::class),
            parentId: 0,
            modified: new \DateTimeImmutable(),
        );

        $articleArray[] = $article;
    }

    public function testAddArticleToArticleTypedArrayByAppend(): void
    {
        $articleArray = new TypedArray(Article::class);

        $article = new Article(
            id: 0,
            title: '',
            content: '',
            breadcrumb: '',
            media: '',
            mediaExtra: '',
            hasContent: true,
            canonical: '',
            orderId: 0,
            children: new TypedArray(ArticleChild::class),
            parentId: 0,
            modified: new \DateTimeImmutable(),
        );
        $articleArray->append($article);

        $this->assertCount(1, $articleArray);
    }

    public function testAddWrongObjectToTypedArrayByAppend(): void
    {
        $this->expectException(InvalidTypedArgumentException::class);

        $articleArray = new TypedArray(ArticleChild::class);

        $article = new Article(
            id: 0,
            title: '',
            content: '',
            breadcrumb: '',
            media: '',
            mediaExtra: '',
            hasContent: true,
            canonical: '',
            orderId: 0,
            children: new TypedArray(ArticleChild::class),
            parentId: 0,
            modified: new \DateTimeImmutable(),
        );

        $articleArray->append($article);
    }


}
