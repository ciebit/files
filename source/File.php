<?php
namespace Ciebit\Files;

use Ciebit\Files\Status;
use DateTime;

abstract class File
{
    /** @var string */
    private $description;

    /** @var DateTime */
    private $datetime;

    /** @var string */
    private $id;

    /** @var string */
    private $mimetype;

    /** @var string */
    private $name;

    /** @var int */
    private $status;

    /** @var float */
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

    public function setSize(float $size): self
    {
        $this->size = $size;
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

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): float
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
}
