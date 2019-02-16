<?php
namespace Ciebit\Files\Images;

use Ciebit\Files\File;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Status;

class Image extends File
{
    /** @var string */
    private $caption;

    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var VariationsCollection */
    private $variations;


    public function __construct(
        string $name,
        string $url,
        string $mimetype,
        int $width,
        int $height,
        status $status
    ) {
        parent::__construct($name, $url, $mimetype, $status);

        $this->height = $height;
        $this->width = $width;
        $this->variations = new VariationsCollection;
    }

    public function setVariations(VariationsCollection $variations): self
    {
        $this->variations = $variations;
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
