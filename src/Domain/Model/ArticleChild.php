<?php

namespace Endeavour\GroeigidsApiClient\Domain\Model;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;

class ArticleChild
{
    /**
     * @var TypedArray<ArticleChild> $children
     */
    public readonly TypedArray $children;

    /**
     * @param array<string, mixed> $children
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $breadcrumb,
        public readonly bool $hasContent,
        array $children = [],
    ) {
        $this->children = new TypedArray(ArticleChild::class, array_map(fn(array $childArray) => new ArticleChild(...$childArray), $children));
    }
}
