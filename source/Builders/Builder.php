<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\Builders\Strategies\Strategy;
use Ciebit\Files\File;

class Builder
{
    private $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function build(): File
    {
        return $this->strategy->build();
    }
}
