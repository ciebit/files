<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\File;
use Ciebit\Files\Builders\Strategies\FromArray;

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
        if (is_array($this->data)) {
            $strategy = (new FromArray)->setData($this->data);
        }
        return (new Builder($strategy))->build();
    }
}
