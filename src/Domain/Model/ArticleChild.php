<?php

namespace Endeavour\GroeigidsApiClient\Domain\Model;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;

class ArticleChild
{
    /**
     * @param TypedArray<ArticleChild> $children
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $breadcrumb,
        public readonly bool $hasContent,
        public readonly TypedArray $children,
    ) {
    }
}