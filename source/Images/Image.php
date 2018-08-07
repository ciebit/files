<?php
declare(strict_types=1);

namespace Ciebit\Files\Images;

use Ciebit\Files\File;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Status;

class Image extends File
{
    private $height; #int
    private $width; #int
	private $caption; #string
    private $variations; #Variations

    public function __construct(
        string $name,
        string $mimetype,
        string $uri,
        int $width,
        int $height,
        status $status
    ) {
        parent::__construct($name, $uri, $mimetype, $status);

        $this->height = $height;
        $this->width = $width;
    }

    public function setVariations(VariationsCollection $variations): self
    {
        $this->variations = $variations;
        return $this;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getVariations(): VariationsCollection
    {
        return $this->variations;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
