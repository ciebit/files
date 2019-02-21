<?php
namespace Ciebit\Files\Images\Builders;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Builders\Strategy;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Images\Variations\Builders\FromArray as VariationBuilder;
use Ciebit\Files\Status;
use Ciebit\Files\Builders\SetBasicAttributes;
use DateTime;
use Exception;

use function is_array;
use function is_object;

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

        if (is_array($variationsData) || is_object($variationsData)) {
            $variationsBuilder = new VariationBuilder;
            foreach($variationsData as $key => $variation){
                $variationsBuilder->setData((array) $variation);
                $variations->add($key, $variationsBuilder->build());
            }
        }

        return $variations;
    }
}
