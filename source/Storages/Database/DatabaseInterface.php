<?php
namespace Ciebit\Files\Storages\Database;

use Ciebit\Files\File;
use Ciebit\Files\Collection;
use Ciebit\Files\Storages\Storage;

interface DatabaseInterface extends Storage
{
    // public function delete(File $File): self;

    public function get(): ?File;

    public function getAll(): Collection;

    // public function save(File $File): self;
}
