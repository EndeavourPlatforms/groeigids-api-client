<?php

namespace Endeavour\GroeigidsApiClient\Domain\Collection;

use ArrayObject;
use InvalidArgumentException;

/**
 * @template T
 * @extends ArrayObject<int|string, T>
 */
class TypedArray extends ArrayObject
{
    /**
     * @param class-string<T> $type
     * @param array<int|string, T> $data
     */
    public function __construct(public readonly string $type, array $data = [])
    {
        foreach ($data as $key => $item) {
            $this->offsetSet($key, $item);
        }

        parent::__construct($data);
    }

    /**
     * @param int|string|null $key
     * @param T $value
     * @return void
     * @throws InvalidArgumentException If the value is not an instance of the specified type.
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (! is_a($value, $this->type)) {
            throw new InvalidArgumentException("Value must be an instance of {$this->type}");
        }

        parent::offsetSet($key, $value);
    }

    /**
     * @param T $value
     * @return void
     * @throws InvalidArgumentException If the value is not an instance of the specified type.
     */
    public function append(mixed $value): void
    {
        if (! is_a($value, $this->type)) {
            throw new InvalidArgumentException("Value must be an instance of {$this->type}");
        }
        parent::append($value);
    }
}
