<?php

namespace Endeavour\GroeigidsApiClient\Infrastructure\HttpClient;

use DateTimeInterface;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidResponseDataException;
use Endeavour\GroeigidsApiClient\Domain\Exception\NoResponseContentException;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageArticle;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Port\GroeigidsClientInterface;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class GroeigidsClient implements GroeigidsClientInterface
{
    protected const GROEIGIDS_BASE_URI = 'https://groeigids-api.elkander.nl/';
    protected const VERSION = 'v1';

    public function __construct(
        protected ClientInterface $client,
        protected RequestFactoryInterface $requestFactory,
        protected string $apiKey,
    ) {
    }

    /**
     * @inheritDoc
     * @throws ClientExceptionInterface|InvalidArgumentException
     */
    public function fetchArticles(int $page = 0, int $size = 20, array $sort = null): TypedArray
    {
        //TODO: implement sorting
        $queryParameters = [
            'page' => $page,
            'size' => $size,
        ];

        $pageArticle = $this->getObjectByRouteAndQueryParameters(PageArticle::class, 'articles', $queryParameters);

        return $pageArticle->articles;
    }

    /**
     * @inheritDoc
     * @throws ClientExceptionInterface
     */
    public function fetchThemeArticles(bool $withChildren = false): TypedArray
    {
        $queryParameters = [
            'includeChildren' => $withChildren ? 'true' : 'false',
        ];

        $request = $this->createGetRequestByRoute('themes', $queryParameters);
        $responseData = $this->getResponseData($request);

        $themeArticles = array_map(fn(array $articleThemeArray) => new Article(...$articleThemeArray), $responseData);

        return new TypedArray(Article::class, $themeArticles);
    }

    /**
     * @throws ClientExceptionInterface|InvalidArgumentException
     */
    public function fetchArticle(int $id, bool $includeChildren = false): Article
    {
        $queryParameters = [
            'includeChildren' => $includeChildren ? 'true' : 'false',
        ];
        $route = 'article/' . $id;

        return $this->getObjectByRouteAndQueryParameters(Article::class, $route, $queryParameters);
    }

    /**
     * @inheritDoc
     * @throws ClientExceptionInterface|InvalidArgumentException
     */
    public function fetchModfifiedArticlesAfterDate(
        DateTimeInterface $dateTime,
        int $page = 0,
        int $size = 20,
        array $sort = null,
    ): TypedArray {
        //TODO: implement sorting
        $queryParameters = [
            'modified' => $dateTime->format('Y-m-d'),
            'page' => $page,
            'size' => $size,
        ];

        $pageArticle = $this->getObjectByRouteAndQueryParameters(PageArticle::class, 'articles/modified', $queryParameters);

        return $pageArticle->articles;
    }

    public function fetchArticleByBreadcrumb(string $breadcrumb): Article
    {
        $queryParameters = [
            'breadcrumb' => $breadcrumb,
        ];

        return $this->getObjectByRouteAndQueryParameters(Article::class, 'theme', $queryParameters);
    }

    /**
     * @param RequestInterface $request
     * @return array<string, mixed>
     * @throws ClientExceptionInterface
     * @throws NoResponseContentException
     */
    protected function getResponseData(RequestInterface $request): array
    {
        $response = $this->client->sendRequest($request);

        $responseData = json_decode($response->getBody()->getContents(), true);

        if (! $responseData) {
            throw new NoResponseContentException('No response content found');
        }

        return $responseData;
    }

    /**
     * @template T of object
     * @param array<string, string|int|bool> $queryParameters
     * @param class-string<T> $type
     * @return T
     * @throws ClientExceptionInterface|InvalidResponseDataException
     */
    protected function getObjectByRouteAndQueryParameters(
        string $type,
        string $route,
        array $queryParameters = []
    ): mixed {
        $request = $this->createGetRequestByRoute($route, $queryParameters);
        $responseData = $this->getResponseData($request);

        try {
            $object = new $type(...$responseData);
        } catch (Throwable) {
            throw new InvalidResponseDataException('Invalid response data');
        }

        return $object;
    }

    protected function getUriString(): string
    {
        return self::GROEIGIDS_BASE_URI . self::VERSION;
    }

    /**
     * @param array<string, string|int> $queryParameters
     */
    protected function createGetRequestByRoute(string $route, array $queryParameters = []): RequestInterface
    {
        $queryString = http_build_query($queryParameters);

        return $this->requestFactory
            ->createRequest(
                'GET',
                sprintf('%s/%s?%s', $this->getUriString(), $route, $queryString)
            )
            ->withHeader('api-key', $this->apiKey)
        ;
    }
}