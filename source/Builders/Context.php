<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\File;

use Ciebit\Files\Images\Builders\FromArray as ImageBuilder;
use Ciebit\Files\Pdfs\Builders\FromArray as PdfBuilder;
use Ciebit\Files\Unknown\Builders\FromArray as UnknownBuilder;

class Context
{
    private $data; #any

    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): File
    {
        if (preg_match('/^image\//', $this->data['mimetype'])) {
            $strategy = (new ImageBuilder)->setData($this->data);
        } else if (preg_match('/\/pdf$/', $this->data['mimetype'])) {
            $strategy = (new PdfBuilder)->setData($this->data);
        } else {
            $strategy = (new UnknownBuilder)->setData($this->data);
        }
        return (new Builder($strategy))->build();
    }
}
