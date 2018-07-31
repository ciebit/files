<?php
namespace Ciebit\Files\Images\Storages;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Images\Collection as ImagesCollection;

interface Storage
{
    public function delete(Image $Image): self;

    public function getAll(): ImagesCollection;

    public function get(): ?Image;

    public function save(Image $Image): self;
}
