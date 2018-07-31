<?php
namespace Ciebit\Files\Storages\FileSystem;

use Ciebit\Files\Storages\Storage;

interface FileSystem extends Storage
{
    public function has(string $nameFile): bool;

    public function save(string $path, string $nameFile): self;
}
