<?php

namespace Endeavour\GroeigidsApiClient\Domain\Collection;

use ArrayObject;
use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidTypedArgumentException;

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
     * @throws InvalidTypedArgumentException
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (! is_a($value, $this->type)) {
            throw new InvalidTypedArgumentException("Value must be an instance of {$this->type}");
        }

        parent::offsetSet($key, $value);
    }

    /**
     * @param T $value
     * @return void
     * @throws InvalidTypedArgumentException
     */
    public function append(mixed $value): void
    {
        if (! is_a($value, $this->type)) {
            throw new InvalidTypedArgumentException("Value must be an instance of {$this->type}");
        }
        parent::append($value);
    }
}
