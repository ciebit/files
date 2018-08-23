<?php
declare(strict_types=1);

namespace Ciebit\Files\Pdfs\Builders;

use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Builders\Strategy;
use Ciebit\Files\Status;
use Ciebit\Files\Builders\SetBasicAttributes;
use DateTime;
use Exception;

class FromArray implements Strategy
{
    use SetBasicAttributes;

    private $data; #:array

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): Pdf
    {
        $status = is_array($this->data)
        && isset($this->data['mimetype'])
        && isset($this->data['name'])
        && isset($this->data['status'])
        && isset($this->data['uri']);

        if (! $status) {
            throw new Exception('ciebit.files.pdfs.builders.invalid', 1);
        }

        $pdf = new Pdf(
            $this->data['name'],
            $this->data['mimetype'],
            $this->data['uri'],
            new Status((int) $this->data['status'])
        );

        $this->setBasicAttributes($pdf, $this->data);

        return $pdf;
    }
}
