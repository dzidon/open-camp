<?php

namespace App\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\CampImage;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class CampImagesCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_image.create';

    private CampImagesUploadData $data;

    /** @var CampImage[] */
    private array $campImages = [];

    public function __construct(CampImagesUploadData $data)
    {
        $this->data = $data;
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
        return $this->campImages;
    }

    /**
     * @param CampImage[] $campImages
     * @return self
     */
    public function setCampImages(array $campImages): self
    {
        foreach ($campImages as $campImage)
        {
            if (!$campImage instanceof CampImage)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, CampImage::class)
                );
            }
        }

        $this->campImages = $campImages;

        return $this;
    }
}