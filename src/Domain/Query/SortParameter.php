<?php

declare(strict_types=1);

namespace Endeavour\GroeigidsApiClient\Domain\Query;

use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidSortDirectionException;

class SortParameter
{
    public function __construct(
        private readonly string $field,
        private readonly string $direction,
    ) {
        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidSortDirectionException(sprintf('Invalid direction for sorting: %s', $direction));
        }
    }

    public function __toString(): string
    {
        return sprintf('%s,%s', $this->field, $this->direction);
    }
}
