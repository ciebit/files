<?php
namespace Ciebit\Files\Images\Variations\Builders;

use Ciebit\Files\Images\Variations\Variation;

interface Builder
{
    public function build(): Variation;
}
