<?php
namespace Ciebit\Files;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\File;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    /** @var ArrayObject */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayObject;
    }

    public function add(File $file): self
    {
        $this->items->append($file);
        return $this;
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->items;
    }

    public function getById(int $id): ?File
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->items->getIterator();
    }
}
