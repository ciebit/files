<?php
declare(strict_types=1);

namespace Ciebit\Files\Unknown;

use Ciebit\Files\File;
use Ciebit\Files\Status;
use DateTime;

class Unknown extends File
{
    public function __construct(
        string $name,
        string $uri,
        string $mimetype,
        Status $status
    ) {
        parent::__construct($name, $uri, $mimetype, $status);
    }
}
