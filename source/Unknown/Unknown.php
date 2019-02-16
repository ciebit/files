<?php
namespace Ciebit\Files\Unknown;

use Ciebit\Files\File;
use Ciebit\Files\Status;
use DateTime;

class Unknown extends File
{
    public function __construct(
        string $name,
        string $url,
        string $mimetype,
        Status $status
    ) {
        parent::__construct($name, $url, $mimetype, $status);
    }

    public function getMetadata(): string
    {
        return '{}';
    }
}
