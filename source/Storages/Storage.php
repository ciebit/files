<?php
namespace Ciebit\Files\Storages;

use Ciebit\Files\Collection;
use Ciebit\Files\File;
use Ciebit\Files\Status;
use DateTime;

interface Storage
{
    public function addFilterByDateTime(string $operator, DateTime ...$values): self;

    public function addFilterByDescription(string $operator, string ...$values): self;

    public function addFilterById(string $operator, string ...$values): self;

    public function addFilterByMimetype(string $operator, string ...$values): self;

    public function addFilterByName(string $operator, string ...$values): self;

    public function addFilterBySize(string $operator, int ...$values): self;

    public function addFilterByStatus(string $operator, Status ...$values): self;

    public function addFilterByUrl(string $operator, string ...$values): self;

    public function addFilterByViews(string $operator, int ...$values): self;

    public function destroy(File $File): self;

    public function findAll(): Collection;

    public function findOne(): ?File;

    // public function save(File $File): self;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

    public function store(File $File): self;
}
