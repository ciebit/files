<?php
declare(strict_types=1);

namespace Ciebit\Files\Images\Factories;

use Ciebit\Files\Images\Image;

interface Builder
{
    public function build(): Image;
}
