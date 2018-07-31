<?php
namespace Ciebit\Files\Images\Variations;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\Images\Variations\Variation;

class Collection
{
    private $variations; #: Variation

    public function __construct()
    {
        $this->variations = new ArrayObject;
    }

    public function add(Variation $variation): self
    {
        $this->append($variation);
        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->variations;
    }

    public function getById(int $id): ?Variation
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $variation) {
            if ($variation->getId() == $id) {
                return $variation;
            }
        }

        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->variations->getIterator();
    }
}
