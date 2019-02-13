<?php
namespace Ciebit\Files\Pdfs;

use Ciebit\Files\File;
use Ciebit\Files\Status;

class Pdf extends File
{
    public function __construct(
        string $name,
        string $mimetype,
        string $uri,
        status $status
    ) {
        parent::__construct($name, $uri, $mimetype, $status);
    }
}
