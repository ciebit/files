<?php
namespace Ciebit\Files\Test\Images\Variations;

use Ciebit\Files\Images\Variations\Variation;
use PHPUnit\Framework\TestCase;

use function json_encode;

class VariationTest extends TestCase
{
    /** @var int */
    private const HEIGHT = 600;

    /** @var int */
    private const SIZE = 1024;

    /** @var string */
    private const URL = 'url.jpg';

    /** @var int */
    private const WIDTH = 1000;

    public function getVariation(): Variation
    {
        return new Variation(self::URL, self::WIDTH, self::HEIGHT, self::SIZE);
    }


    public function testCreateManual(): void
    {
        $variation = $this->getVariation();

        $this->assertEquals(self::URL, $variation->getUrl());
        $this->assertEquals(self::WIDTH, $variation->getWidth());
        $this->assertEquals(self::HEIGHT, $variation->getHeight());
        $this->assertEquals(self::SIZE, $variation->getSize());
    }

    public function testJsonSerialize(): void
    {
        $variation = $this->getVariation();

        $data = [
            'height' => self::HEIGHT,
            'size' => self::SIZE,
            'url' => self::URL,
            'width' => self::WIDTH
        ];
        $json = json_encode($variation);

        $this->assertJsonStringEqualsJsonString(json_encode($data), $json);
    }
}
