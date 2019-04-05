<?php
namespace Ciebit\Files;

use Ciebit\Files\Status;
use DateTime;
use JsonSerializable;

abstract class File implements JsonSerializable
{
    /** @var string */
    private $description;

    /** @var DateTime */
    private $datetime;

    /** @var string */
    private $id;

    /** @var array */
    private $labelsId;

    /** @var array */
    private $metadata;

    /** @var string */
    private $mimetype;

    /** @var string */
    private $name;

    /** @var int */
    private $status;

    /** @var int */
    private $size;

    /** @var string */
    private $url;

    /** @var int */
    private $views;


    public function __construct(
        string $name,
        string $url,
        string $mimetype,
        Status $status
    ) {
        $this->description = '';
        $this->datetime = new DateTime;
        $this->id = '';
        $this->labelsId = [];
        $this->metadata = [];
        $this->mimetype = $mimetype;
        $this->name = $name;
        $this->size = 0;
        $this->status = $status;
        $this->url = $url;
        $this->views = 0;
    }

    public function setDateTime(DateTime $datetime): self
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setLabelsId(array $ids): self
    {
        $this->labelsId = $ids;
        return $this;
    }

    public function setSize(int $bytes): self
    {
        $this->size = $bytes;
        return $this;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
        return $this;
    }

    public function getDateTime(): DateTime
    {
        return $this->datetime;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabelsId(): array
    {
        return $this->labelsId;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'datetime' => $this->getDateTime()->format('Y-m-d H:i:s'),
            'id' => $this->id,
            'labelsId' => $this->labelsId,
            'metadata' => $this->metadata,
            'mimetype' => $this->mimetype,
            'name' => $this->name,
            'status' => $this->status,
            'size' => $this->size,
            'url' => $this->url,
            'views' => $this->views,
        ];
    }
}
