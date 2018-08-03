<?php
declare(strict_types=1);

namespace Ciebit\Files;

use MyCLabs\Enum\Enum;

class Status extends Enum
{
    const ACTIVE = 3;
    const ANALYZE = 2;
    const DRAFT = 1;
    const TRASH = 4;
    const INACTIVE = 5;
}
