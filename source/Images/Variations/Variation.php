<?php
namespace Ciebit\Files\Images\Variations;

use function json_encode;

class Variation
{
    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var int */
    private $size;

    /** @var string */
    private $url;


    public function __construct(string $url, int $width, int $height, int $size)
    {
        $this->url = $url;
        $this->height = $height;
        $this->width = $width;
        $this->size = $size;
    }

    public function __toString(): string
    {
        return json_encode([
            'height' => $this->getHeight(),
            'size' => $this->getSize(),
            'url' => $this->getUrl(),
            'width' => $this->getWidth()
        ]);
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
