<?php

declare(strict_types=1);

namespace Endeavour\GroeigidsApiClient\Domain\Query;

use Endeavour\GroeigidsApiClient\Domain\Collection\TypedArray;

class QueryParameters
{
    /**
     * @param TypedArray<SortParameter>|null $sortParameters
     */
    public function __construct(
        private readonly ?int $page = null,
        private readonly ?int $size = null,
        private readonly ?string $breadcrumb = null,
        private readonly ?\DateTimeInterface $modified = null,
        private readonly ?bool $includeChildren = null,
        private readonly ?TypedArray $sortParameters = null,
    ) {
    }

    public function toQueryString(): string
    {
        $sortStrings = $this->buildSortStrings($this->sortParameters);

        if (is_bool($this->includeChildren)) {
            $includeChildrenString = $this->includeChildren ? 'true' : 'false';
        }

        $queryArray = [
            'page' => $this->page,
            'size' => $this->size,
            'modified' => $this->modified?->format('Y-m-d') ?? null,
            'breadcrumb' => $this->breadcrumb,
            'includeChildren' => $includeChildrenString ?? null,
            'sort' => $sortStrings,
        ];

        $queryArray = array_filter($queryArray);

        $queryString = http_build_query($queryArray, '', '&', PHP_QUERY_RFC3986);

        return str_replace(['%5B0%5D', '%5B1%5D'], '', $queryString);
    }

    /**
     * @param TypedArray<SortParameter>|null $sortParameters
     * @return string[] | null
     */
    protected function buildSortStrings(?TypedArray $sortParameters): ?array
    {
        if ($sortParameters === null) {
            return null;
        }

        $sortStrings = [];

        foreach($sortParameters as $sortParameter) {
            $sortStrings[] = (string) $sortParameter;
        }

        return $sortStrings;
    }
}
