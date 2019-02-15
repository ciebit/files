<?php
namespace Ciebit\Files\Images\Variations;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\Images\Variations\Variation;
use Countable;
use IteratorAggregate;

use function count;

class Collection implements Countable, IteratorAggregate
{
    /** @var array */
    private $variations;

    public function __construct()
    {
        $this->variations = [];
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

    public function find(string $key): ?Variation
    {
        return $this->variations[$key] ?? null;
    }

    public function getArrayObject(): ArrayObject
    {
        return new ArrayObject($this->variations);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->variations);
    }
}
