<?php
declare(strict_types=1);

namespace Ciebit\Files\Images\Variations;

class Variation
{
    private $height; #:int
    private $width; #:int
    private $size; #:float
    private $uri; #:string

    public function __construct(string $uri, int $height, int $width, float $size)
    {
        $this->uri = $uri;
        $this->height = $height;
        $this->width = $width;
        $this->size = $size;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
