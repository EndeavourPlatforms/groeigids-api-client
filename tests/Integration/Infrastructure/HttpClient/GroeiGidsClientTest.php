<?php

namespace Endeavour\GroeigidsApiClient\Test\Integration\Infrastructure\HttpClient;

use DateTime;
use Endeavour\GroeigidsApiClient\Domain\Builder\ResponseDataBuilder;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidResponseDataException;
use Endeavour\GroeigidsApiClient\Domain\Exception\NoResponseContentException;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Port\GroeigidsClientInterface;
use Endeavour\GroeigidsApiClient\Domain\Validator\ResponseValidator;
use Endeavour\GroeigidsApiClient\Infrastructure\HttpClient\GroeigidsClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class GroeiGidsClientTest extends TestCase
{
    protected GroeigidsClientInterface $client;
    protected function setUp(): void
    {
        $httpFactory = new HttpFactory();
        $guzzleClient = new Client();
        $responseDataBuilder = new ResponseDataBuilder();
        $responseDataValidator = new ResponseValidator(new Validator());
        $apiKey = $_ENV['GROEIGIDS_API_KEY'];

        if (! $apiKey) {
            $this->throwException(new \Exception('No API key provided'));
        }

        $this->client = new GroeigidsClient(
            $guzzleClient,
            $httpFactory,
            $responseDataBuilder,
            $responseDataValidator,
            $apiKey
        );

        parent::setUp();
    }

    public function testFetchArticles(): void
    {
        $articles = $this->client->fetchArticles();

        $this->assertInstanceOf(TypedArray::class, $articles);
    }

    public function testFetchArticleByBreadcrumb(): void
    {
        $breadcrumb = '/onderwerp/zwanger/';
        $theme = $this->client->fetchArticleByBreadcrumb($breadcrumb);

        $this->assertInstanceOf(Article::class, $theme);
    }

    public function testFetchArticleWithChildren(): void
    {
        $id = 7808;
        $article = $this->client->fetchArticle($id, true);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testFetchArticleWithChildrenShouldNotBeEmpty(): void
    {
        $id = 7808;
        $article = $this->client->fetchArticle($id, true);

        $this->assertNotEmpty($article->children);
    }

    public function testFetchArticleWithoutChildren(): void
    {
        $id = 7808;
        $article = $this->client->fetchArticle($id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testFetchArticleWithChildrenShouldBeEmpty(): void
    {
        $id = 7808;
        $article = $this->client->fetchArticle($id);

        $this->assertEmpty($article->children);
    }

    public function testFetchModifiedArticles(): void
    {
        $datetime = new DateTime('2000-01-01');
        $articles = $this->client->fetchModfifiedArticlesAfterDate($datetime);

        $this->assertNotEmpty($articles);
    }

    public function testFetchThemeArticles(): void
    {
        $themes = $this->client->fetchThemeArticles(true);

        $this->assertNotEmpty($themes);
    }

    public function testFetchThemeArticlesWithoutChildren(): void
    {
        $themes = $this->client->fetchThemeArticles();
        foreach($themes as $theme) {
            $this->assertEmpty($theme->children);
        }
    }

    public function testFetchThemeArticlesWithChildren(): void
    {
        $themes = $this->client->fetchThemeArticles(true);
        foreach($themes as $theme) {
            $this->assertNotEmpty($theme->children);
        }
    }

    public function testInvalidResponseData(): void
    {
        $this->expectException(InvalidResponseDataException::class);
        $this->client->fetchArticle(-1);
    }

    public function testNoResponseContent(): void
    {
        $httpFactory = new HttpFactory();
        $guzzleClient = new Client();
        $responseDataBuilder = new ResponseDataBuilder();
        $responseDataValidator = new ResponseValidator(new Validator());

        $client = new GroeigidsClient(
            $guzzleClient,
            $httpFactory,
            $responseDataBuilder,
            $responseDataValidator,
            ''
        );

        $this->expectException(NoResponseContentException::class);
        $client->fetchArticles();
    }
}