<?php
declare(strict_types=1);

namespace Ciebit\Files\Images\Builders;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Builders\Strategy;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
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

    public function build(): Image
    {
        $metadata = json_decode($this->data['metadata']);

        $status = is_array($this->data)
        && isset($metadata->height)
        && isset($this->data['mimetype'])
        && isset($this->data['name'])
        && isset($this->data['status'])
        && isset($this->data['url'])
        && isset($metadata->width);

        if (! $status) {
            throw new Exception('ciebit.files.images.builders.invalid', 1);
        }

        $image = new Image(
            $this->data['name'],
            $this->data['url'],
            $this->data['mimetype'],
            (int) $metadata->width,
            (int) $metadata->height,
            new Status((int) $this->data['status'])
        );

        $this->setBasicAttributes($image, $this->data);

        if (isset($metadata->variations)) {
            $image->setVariations(
                $this->standardizeVariations(
                    $metadata->variations
                )
            );
        }

        return $image;
    }

    private function standardizeVariations($variationsData): VariationsCollection
    {
        if ($variationsData instanceof VariationsCollection) {
            return $variationsData;
        }

        $variations = new VariationsCollection;

        if (is_array($variationsData)) {
            $variationsBuilder = new VariationBuilder;
            foreach($variationsData as $variation){
                $variationsBuilder->setData($variation);
                $variations->add($variationsBuilder->build());
            }
        }

        return $variations;
    }
}
