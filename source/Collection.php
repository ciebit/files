<?php
declare(strict_types=1);

namespace Ciebit\Files;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\File;

class Collection
{
    private $files; #: ArrayObject

    public function __construct()
    {
        $this->files = new ArrayObject;
    }

    public function add(File $file): self
    {
        $this->files->append($file);
        return $this;
    }

    public function getById(int $id): ?File
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $file) {
            if ($file->getId() == $id) {
                return $file;
            }
        }

        return null;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->files;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->files->getIterator();
    }
}
