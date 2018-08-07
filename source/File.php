<?php
declare(strict_types=1);

namespace Ciebit\Files;

use Ciebit\Files\Status;
use DateTime;

abstract class File
{
    private $description; #:string
    private $date_hour; #:DateTime
    private $extension; #:string
    private $id; #:int
    private $mimetype; #:string
    private $name; #:string
    private $status; #:int
    private $size; #:float
    private $uri; #:string
    private $views; #:int

    public function __construct(
        string $name,
        string $uri,
        string $mimetype,
        Status $status
    ) {
        $this->description = '';
        $this->date_hour = new DateTime;
        $this->extension = '';
        $this->id = 0;
        $this->mimetype = $mimetype;
        $this->name = $name;
        $this->size = 0;
        $this->status = $status;
        $this->uri = $uri;
        $this->views = 0;
    }

    public function setDateHour(DateTime $date_hour): self
    {
        $this->date_hour = $date_hour;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setMimetype(string $mimetype): self
    {
        $this->mimetype = $mimetype;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
        return $this;
    }

    public function getDateHour(): DateTime
    {
        return $this->date_hour;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getId(): int
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

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getViews(): int
    {
        return $this->views;
    }
}
