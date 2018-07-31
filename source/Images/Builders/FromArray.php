<?php
declare(strict_types=1);

namespace Ciebit\Files\Images\Builders;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Images\Builders\Builder;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Status;
use DateTime;
use Exception;

class FromArray implements Builder
{
    private $data; #:array

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): Image
    {
        $status = is_array($this->data)
        && isset($this->data['height'])
        && isset($this->data['mimetype'])
        && isset($this->data['name'])
        && isset($this->data['status'])
        && isset($this->data['uri'])
        && isset($this->data['width']);

        if (! $status) {
            throw new Exception('ciebit.files.builders.invalid', 1);
        }

        $image = new Image(
            $this->data['name'],
            $this->data['mimetype'],
            $this->data['uri'],
            $this->data['width'],
            $this->data['height'],
            new Status($this->data['status'])
        );

        $this->setBasicAttributes($image, $this->data);

        if (isset($this->data['variations'])) {
            $image->setVariations(
                $this->standardizeVariations(
                    $this->data['variations']
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
