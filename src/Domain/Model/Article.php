<?php

namespace Endeavour\GroeigidsApiClient\Domain\Model;

use DateTimeImmutable;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;
use Exception;

class Article
{
    public readonly ?DateTimeImmutable $modified;

    /**
     * @var TypedArray<ArticleChild> $children
     */
    public readonly TypedArray $children;

    /**
     * @param array<string, mixed> $children
     * @throws Exception
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly string $breadcrumb,
        public readonly string $media,
        public readonly string $mediaExtra,
        public readonly bool $hasContent,
        public readonly string $canonical,
        public readonly int $orderId,
        public readonly ?int $parentId = null,
        array $children = [],
        ?string $modified = null,
    ) {
        $this->children = new TypedArray(
            ArticleChild::class,
            array_map(fn(array $childArray) => new ArticleChild(...$childArray), $children)
        );

        $this->modified = $modified ? new DateTimeImmutable($modified) : null;
    }
}
