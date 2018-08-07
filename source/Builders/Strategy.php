<?php
declare(strict_types=1);

namespace Ciebit\Files\Builders;

use Ciebit\Files\File;

interface Strategy
{
    public function build();
}
