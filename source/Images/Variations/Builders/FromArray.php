<?php
namespace Ciebit\Files\Images\Variations\Builders;

use Ciebit\Files\Images\Variations\Builders\Build;
use Ciebit\Files\Images\Variations\Collection;
use Ciebit\Files\Images\Variations\Variation;
use Exception;

class FromArray implements Builder
{
    private $data; #: array

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): Variation
    {
        $status = is_array($this->data)
        && isset($this->data['uri'])
        && isset($this->data['height'])
        && isset($this->data['width'])
        && isset($this->data['size']);

        if (! $status) {
            throw new Exception('ciebit.files.images.variations.builderArray.error', 1);
        }

        return new Variation(
            $this->data['uri'],
            $this->data['height'],
            $this->data['width'],
            $this->data['size']
        );
    }
}
