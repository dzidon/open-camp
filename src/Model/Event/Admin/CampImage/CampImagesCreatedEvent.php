<?php

namespace App\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\CampImage;
use LogicException;
use Symfony\Contracts\EventDispatcher\Event;

class CampImagesCreatedEvent extends Event
{
    public const NAME = 'model.admin.camp_images.created';

    private CampImagesUploadData $data;

    /** @var CampImage[] */
    private array $images;

    public function __construct(CampImagesUploadData $data, array $images)
    {
        foreach ($images as $image)
        {
            if (!$image instanceof CampImage)
            {
                throw new LogicException(
                    sprintf("Images passed to the constructor of %s must all be instances of %s.", self::class, CampImage::class)
                );
            }
        }

        $this->data = $data;
        $this->images = $images;
    }

    public function getCampImagesUploadData(): CampImagesUploadData
    {
        return $this->data;
    }

    public function setCampImagesUploadData(CampImagesUploadData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampImages(): array
    {
        return $this->images;
    }

    public function addCampImage(CampImage $campImage): self
    {
        if (in_array($campImage, $this->images, true))
        {
            return $this;
        }

        $this->images[] = $campImage;

        return $this;
    }

    public function removeCampImage(CampImage $campImage): self
    {
        $key = array_search($campImage, $this->images, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->images[$key]);

        return $this;
    }
}