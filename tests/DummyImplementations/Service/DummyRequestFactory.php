<?php

namespace Endeavour\GroeigidsApiClient\Test\DummyImplementations\Service;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class DummyRequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}