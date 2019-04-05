<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\File;

interface Strategy
{
    public function build(): File;

    public function setData(array $data);
}
