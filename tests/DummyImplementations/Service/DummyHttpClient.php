<?php

namespace Endeavour\GroeigidsApiClient\Test\DummyImplementations\Service;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DummyHttpClient implements ClientInterface
{
    public function __construct(protected readonly string $content)
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return new Response(body: $this->content);
    }
}