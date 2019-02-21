<?php
declare(strict_types=1);

namespace Ciebit\Files\Unknown\Builders;

use Ciebit\Files\Unknown\Unknown;
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

    public function build(): Unknown
    {
        $status = is_array($this->data)
        && isset($this->data['name'])
        && isset($this->data['mimetype'])
        && isset($this->data['status'])
        && isset($this->data['url']);

        if (! $status) {
            throw new Exception('ciebit.files.unknown.builders.invalid', 1);
        }

        $unknown = new Unknown(
            $this->data['name'],
            $this->data['url'],
            $this->data['mimetype'],
            new Status((int) $this->data['status'])
        );

        $this->setBasicAttributes($unknown, $this->data);

        return $unknown;
    }
}
