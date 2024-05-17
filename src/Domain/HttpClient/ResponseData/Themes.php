<?php

namespace Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;

class Themes
{
    /**
     * @param TypedArray<Article> $themes
     */
    public function __construct(public readonly TypedArray $themes)
    {
    }
}