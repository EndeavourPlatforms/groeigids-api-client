<?php

namespace Endeavour\GroeigidsApiClient\Infrastructure\HttpClient;

use DateTimeInterface;
use Endeavour\GroeigidsApiClient\Domain\Builder\ResponseDataBuilderInterface;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidResponseDataException;
use Endeavour\GroeigidsApiClient\Domain\Exception\NoResponseContentException;
use Endeavour\GroeigidsApiClient\Domain\Exception\ObjectTypeNotSupportedException;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageArticle;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\Themes;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Port\GroeigidsClientInterface;
use Endeavour\GroeigidsApiClient\Domain\Validator\ResponseValidatorInterface;
use Exception;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class GroeigidsClient implements GroeigidsClientInterface
{
    protected const GROEIGIDS_BASE_URI = 'https://groeigids-api.elkander.nl/';
    protected const VERSION = 'v1';

    public function __construct(
        protected ClientInterface $client,
        protected RequestFactoryInterface $requestFactory,
        protected ResponseDataBuilderInterface $responseDataBuilder,
        protected ResponseValidatorInterface $responseValidator,
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

        return $this
            ->getObjectByRouteAndQueryParameters(Themes::class, 'themes', $queryParameters)
            ->themes
        ;
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
     * @throws ClientExceptionInterface
     * @throws NoResponseContentException
     */
    protected function getResponseBody(RequestInterface $request): string
    {
        $response = $this->client->sendRequest($request);

        $responseBody = $response->getBody()->getContents();

        if (empty($responseBody)) {
            throw new NoResponseContentException('No response content found');
        }

        return $responseBody;
    }

    /**
     * @template T of object
     * @param array<string, string|int|bool> $queryParameters
     * @param class-string<T> $type
     * @throws ClientExceptionInterface|ObjectTypeNotSupportedException|
     */
    protected function getObjectByRouteAndQueryParameters(
        string $type,
        string $route,
        array $queryParameters = []
    ): PageArticle|Article|Themes {
        $request = $this->createGetRequestByRoute($route, $queryParameters);
        $responseBody = $this->getResponseBody($request);

        try {
            $this->responseValidator->validateData($type, $responseBody);
        } catch (Exception) {
            throw new InvalidResponseDataException('Invalid response data');
        }

        $responseData = json_decode($responseBody, true);

        return match($type) {
            Article::class => $this->responseDataBuilder->buildArticle($responseData),
            PageArticle::class => $this->responseDataBuilder->buildPageArticle($responseData),
            Themes::class => $this->responseDataBuilder->buildThemes($responseData),
            default => throw new ObjectTypeNotSupportedException('Response data could not be converted to object'),
        };
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