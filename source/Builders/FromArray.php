<?php
declare(strict_types=1);

namespace Ciebit\Files\Builders;

use Ciebit\Files\Builders\Build;
use Ciebit\Files\File;

abstract class FromArray implements Build
{
    private $data; #:array

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): File
    {

    }
}
