<?php
namespace Ciebit\Files\Unknown;

use Ciebit\Files\Collection as FileCollection;
use Ciebit\Files\Unknown\Unknown;

class Collection extends FileCollection
{
    public function add(Unknown $unknown): self
    {
        parent::add($unknown);
        return $this;
    }
}
