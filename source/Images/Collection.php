<?php
namespace Ciebit\Files\Images;

use Ciebit\Files\Collection as FileCollection;
use Ciebit\Files\Images\Image;

class Collection extends FileCollection
{
    public function add(Image $Image): self
    {
        parent::add($image);
        return $this;
    }
}
