<?php
namespace Ciebit\Files\Images\Variations;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\Images\Variations\Variation;
use Countable;
use IteratorAggregate;

use function count;
use function json_encode;

class Collection implements Countable, IteratorAggregate
{
    /** @var array */
    private $variations;

    public function __construct()
    {
        $this->variations = [];
    }

    public function __toString(): string
    {
        return json_encode($this->variations);
    }

    public function add(string $key, Variation $variation): self
    {
        $this->variations[$key] = $variation;
        return $this;
    }

    public function count(): int
    {
        return count($this->variations);
    }

    public function getArrayObject(): ArrayObject
    {
        return new ArrayObject($this->variations);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->variations);
    }

    public function findByKey(string $key): ?Variation
    {
        return $this->variations[$key] ?? null;
    }
}
