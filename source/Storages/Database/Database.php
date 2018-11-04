<?php
namespace Ciebit\Files\Storages\Database;

use Ciebit\Files\File;
use Ciebit\Files\Status;
use Ciebit\Files\Collection;
use Ciebit\Files\Storages\Storage;

interface Database extends Storage
{
    public function addFilterById(int $id, string $operator = '='): self;

    public function addFilterByIds(string $operator, int ...$id): self;

    public function addFilterByStatus(Status $status, string $operator = '='): self;

    // public function delete(File $File): self;

    public function get(): ?File;

    public function getAll(): Collection;

    // public function save(File $File): self;

    public function setStartingLine(int $lineInit): self;

    public function setTotalLines(int $total): self;
}
