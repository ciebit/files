<?php
declare(strict_types=1);

namespace Ciebit\Files\Pdfs;

use Ciebit\Files\Collection as FileCollection;
use Ciebit\Files\Pdfs\Pdf;

class Collection extends FileCollection
{
    public function add(Pdf $pdf): self
    {
        parent::add($pdf);
        return $this;
    }
}
