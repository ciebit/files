<?php
namespace Ciebit\Files\Storages;

interface Storage
{
    /** @var string */
    public const FIELD_ID = 'id';

    /** @var string */
    public const FIELD_DATETIME = 'datetime';

    /** @var string */
    public const FIELD_DESCRIPTION = 'description';

    /** @var string */
    public const FIELD_METADATA = 'metadata';

    /** @var string */
    public const FIELD_MIMETYPE = 'mimetype';

    /** @var string */
    public const FIELD_NAME = 'name';

    /** @var string */
    public const FIELD_SIZE = 'size';

    /** @var string */
    public const FIELD_STATUS = 'status';

    /** @var string */
    public const FIELD_URL = 'url';

    /** @var string */
    public const FIELD_VIEWS = 'views';


    public function addFilterByDateTime(string $operator, DateTime ...$values): self;

    public function addFilterByDescription(string $operator, string ...$values): self;

    public function addFilterById(string $operator, string ...$values): self;

    public function addFilterByMimetype(string $operator, string ...$values): self;

    public function addFilterByName(string $operator, string ...$values): self;

    public function addFilterBySize(string $operator, int ...$values): self;

    public function addFilterByStatus(string $operator, Status ...$values): self;

    public function addFilterByUrl(string $operator, string ...$values): self;

    public function addFilterByViews(string $operator, int ...$values): self;

    // public function delete(File $File): self;

    public function findAll(): Collection;

    public function findOne(): ?File;

    // public function save(File $File): self;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

}
