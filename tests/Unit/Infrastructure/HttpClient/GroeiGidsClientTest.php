<?php

namespace Endeavour\GroeigidsApiClient\Test\Unit\Infrastructure\HttpClient;

use DateTime;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidResponseDataException;
use Endeavour\GroeigidsApiClient\Domain\Exception\NoResponseContentException;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Port\GroeigidsClientInterface;
use Endeavour\GroeigidsApiClient\Infrastructure\HttpClient\GroeigidsClient;
use Endeavour\GroeigidsApiClient\Test\DummyImplementations\Service\DummyHttpClient;
use Endeavour\GroeigidsApiClient\Test\DummyImplementations\Service\DummyRequestFactory;
use PHPUnit\Framework\TestCase;

class GroeiGidsClientTest extends TestCase
{
    protected function getClient(string $responseFilePath): GroeigidsClientInterface
    {
        $httpFactory = new DummyRequestFactory();
        $guzzleClient = new DummyHttpClient(
            file_get_contents(sprintf(__DIR__ . '/../../../Resources/%s', $responseFilePath))
        );
        $apiKey = '';

        return new GroeigidsClient($guzzleClient, $httpFactory, $apiKey);
    }

    public function testFetchArticles(): void
    {
        $articles = $this
            ->getClient('groeigids-api.elkander.nl-v1-articles?page=0&size=20.json')
            ->fetchArticles()
        ;

        $this->assertInstanceOf(TypedArray::class, $articles);
    }

    public function testFetchArticleByBreadcrumb(): void
    {
        $breadcrumb = '/onderwerp/zwanger/';
        $theme = $this
            ->getClient('groeigids-api.elkander.nl-v1-theme?breadcrumb=%2Fonderwerp%2Fzwanger%2F.json')
            ->fetchArticleByBreadcrumb($breadcrumb);

        $this->assertInstanceOf(Article::class, $theme);
    }

    public function testFetchArticleWithChildren(): void
    {
        $id = 7808;
        $article = $this
            ->getClient('groeigids-api.elkander.nl-v1-article-7808?includeChildren=true.json')
            ->fetchArticle($id, true)
        ;

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testFetchArticleWithChildrenShouldNotBeEmpty(): void
    {
        $id = 7808;
        $article = $this
            ->getClient('groeigids-api.elkander.nl-v1-article-7808?includeChildren=true.json')
            ->fetchArticle($id, true)
        ;

        $this->assertNotEmpty($article->children);
    }

    public function testFetchArticleWithoutChildren(): void
    {
        $id = 7808;
        $article = $this
            ->getClient('groeigids-api.elkander.nl-v1-article-7808?includeChildren=false.json')
            ->fetchArticle($id)
        ;

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testFetchArticleWithChildrenShouldBeEmpty(): void
    {
        $id = 7808;
        $article = $this
            ->getClient('groeigids-api.elkander.nl-v1-article-7808?includeChildren=false.json')
            ->fetchArticle($id)
        ;

        $this->assertEmpty($article->children);
    }

    public function testFetchModifiedArticles(): void
    {
        $datetime = new DateTime('2000-01-01');
        $articles = $this
            ->getClient('groeigids-api.elkander.nl-v1-articles-modified?modified=2000-01-01&page=0&size=20.json')
            ->fetchModfifiedArticlesAfterDate($datetime);

        $this->assertNotEmpty($articles);
    }

    public function testFetchThemeArticles(): void
    {
        $themes = $this
            ->getClient('groeigids-api.elkander.nl-v1-themes?includeChildren=true.json')
            ->fetchThemeArticles(true)
        ;

        $this->assertNotEmpty($themes);
    }

    public function testFetchThemeArticlesWithoutChildren(): void
    {
        $themes = $this
            ->getClient('groeigids-api.elkander.nl-v1-themes?includeChildren=false.json')
            ->fetchThemeArticles()
        ;
        foreach($themes as $theme) {
            $this->assertEmpty($theme->children);
        }
    }

    public function testFetchThemeArticlesWithChildren(): void
    {
        $themes = $this
            ->getClient('groeigids-api.elkander.nl-v1-themes?includeChildren=true.json')
            ->fetchThemeArticles(true)
        ;

        foreach($themes as $theme) {
            $this->assertNotEmpty($theme->children);
        }
    }

    public function testInvalidResponseData(): void
    {
        $id = -1;
        $this->expectException(InvalidResponseDataException::class);
        $this
            ->getClient('groeigids-api.elkander.nl-v1-article--1?includeChildren=false.json')
            ->fetchArticle($id)
        ;
    }

    public function testNoResponseContent(): void
    {
        $this->expectException(NoResponseContentException::class);
        $this->getClient('')->fetchArticles();
    }
}