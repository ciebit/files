<?php
namespace Ciebit\Files\Helpers;

use Ciebit\Files\Builders\Context;
use Ciebit\Files\File;

use function array_merge;
use function json_decode;
use function json_encode;

class Replicator
{
    public function replicate(File $file, array $variations = []): File
    {
        $data = json_decode(json_encode($file), true);
        $data = array_merge($data, $variations);
        return (new Context)->setData($data)->build();
    }
}
