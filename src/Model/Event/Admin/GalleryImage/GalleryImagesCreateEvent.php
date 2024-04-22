<?php

namespace App\Model\Event\Admin\GalleryImage;

use App\Library\Data\Admin\GalleryImagesUploadData;
use App\Model\Entity\GalleryImage;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class GalleryImagesCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image.create';

    private GalleryImagesUploadData $data;

    /** @var GalleryImage[] */
    private array $galleryImages = [];

    public function __construct(GalleryImagesUploadData $data)
    {
        $this->data = $data;
    }

    public function getGalleryImagesUploadData(): GalleryImagesUploadData
    {
        return $this->data;
    }

    public function setGalleryImagesUploadData(GalleryImagesUploadData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getGalleryImages(): array
    {
        return $this->galleryImages;
    }

    /**
     * @param GalleryImage[] $galleryImages
     * @return self
     */
    public function setGalleryImages(array $galleryImages): self
    {
        foreach ($galleryImages as $galleryImage)
        {
            if (!$galleryImage instanceof GalleryImage)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, GalleryImage::class)
                );
            }
        }

        $this->galleryImages = $galleryImages;

        return $this;
    }
}