<?php
namespace Ciebit\Files\Pdfs;

use Ciebit\Files\File;
use Ciebit\Files\Status;

class Pdf extends File
{
    public function __construct(
        string $name,
        string $url,
        status $status
    ) {
        parent::__construct($name, $url, 'application/pdf', $status);
    }
}
