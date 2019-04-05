<?php
namespace Ciebit\Files\Images\Builders;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Builders\Strategy;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Images\Variations\Builders\FromArray as VariationBuilder;
use Ciebit\Files\Status;
use Ciebit\Files\File;
use Ciebit\Files\Builders\SetBasicAttributes;
use DateTime;
use Exception;

use function is_array;
use function is_object;

class FromArray implements Strategy
{
    use SetBasicAttributes;

    /** @var array */
    private $data;

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): File
    {
        $width = $this->data['width'] ?? ($this->data['metadata']['width'] ?? false);
        $height = $this->data['height'] ?? ($this->data['metadata']['height'] ?? false);
        $variations = $this->data['variations'] ?? ($this->data['metadata']['variations'] ?? false);

        $status = is_array($this->data)
        && is_numeric($height)
        && isset($this->data['mimetype'])
        && isset($this->data['name'])
        && isset($this->data['status'])
        && isset($this->data['url'])
        && is_numeric($width);

        if (! $status) {
            throw new Exception('ciebit.files.images.builders.invalid', 1);
        }

        $image = new Image(
            $this->data['name'],
            $this->data['url'],
            $this->data['mimetype'],
            (int) $width,
            (int) $height,
            new Status((int) $this->data['status'])
        );

        if (isset($this->data['labelsId']) && is_array($this->data['labelsId'])) {
            $image->setLabelsId($this->data['labelsId']);
        }

        $this->setBasicAttributes($image, $this->data);

        if ($variations != false) {
            $image->setVariations(
                $this->standardizeVariations(
                    $variations
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
