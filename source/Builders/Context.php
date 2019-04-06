<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\File;

use Ciebit\Files\Builders\Strategy;
use Ciebit\Files\Images\Builders\FromArray as ImageBuilder;
use Ciebit\Files\Pdfs\Builders\FromArray as PdfBuilder;
use Ciebit\Files\Unknown\Builders\FromArray as UnknownBuilder;

class Context
{
    /** @var array */
    private $data = [];

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): File
    {
        $mimetype = $this->data['mimetype'] ?? '';
        $strategy = $this->discoveryStrategy($mimetype);
        $strategy->setData($this->data);

        return (new Builder($strategy))->build();
    }

    private function discoveryStrategy(string $mimetype): Strategy
    {
        if (preg_match('/^image\//', $mimetype)) {
            return new ImageBuilder;
        }

        if (preg_match('/\/pdf$/', $mimetype)) {
            return new PdfBuilder;
        }

        return new UnknownBuilder;
    }
}
