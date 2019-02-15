<?php
namespace Ciebit\Files\Images\Variations;

class Variation
{
    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var float */
    private $size;

    /** @var string */
    private $uri;


    public function __construct(string $uri, int $height, int $width, float $size)
    {
        $this->uri = $uri;
        $this->height = $height;
        $this->width = $width;
        $this->size = $size;
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
