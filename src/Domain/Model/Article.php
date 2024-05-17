<?php

namespace Endeavour\GroeigidsApiClient\Domain\Model;

use DateTimeImmutable;
use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;

class Article
{
    /**
     * @param TypedArray<ArticleChild> $children
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
        public readonly TypedArray $children,
        public readonly ?int $parentId,
        public readonly ?DateTimeImmutable $modified,
    ) {
    }
}
