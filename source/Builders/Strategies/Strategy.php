<?php
declare(strict_types=1);

namespace Ciebit\Files\Builders\Strategies;

use Ciebit\Files\File;

interface Strategy
{
    public function build(): File;
}
