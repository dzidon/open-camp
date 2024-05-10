<?php

namespace App\Model\Event\Admin\GalleryImageCategory;

use App\Library\Data\Admin\GalleryImageCategoryTruncateData;
use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class GalleryImageCategoryTruncateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image_category.truncate';

    private GalleryImageCategoryTruncateData $data;

    private GalleryImageCategory $entity;

    private array $removedGalleryImages = [];

    public function __construct(GalleryImageCategoryTruncateData $data, GalleryImageCategory $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getGalleryImageCategoryTruncateData(): GalleryImageCategoryTruncateData
    {
        return $this->data;
    }

    public function setGalleryImageCategoryTruncateData(GalleryImageCategoryTruncateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getGalleryImageCategory(): GalleryImageCategory
    {
        return $this->entity;
    }

    public function setGalleryImageCategory(GalleryImageCategory $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return GalleryImage[]
     */
    public function getRemovedGalleryImages(): array
    {
        return $this->removedGalleryImages;
    }

    /**
     * @param GalleryImage[] $removedGalleryImages
     * @return $this
     */
    public function setRemovedGalleryImages(array $removedGalleryImages): self
    {
        foreach ($removedGalleryImages as $removedGalleryImage)
        {
            if (!$removedGalleryImage instanceof GalleryImage)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, GalleryImage::class)
                );
            }
        }

        $this->removedGalleryImages = $removedGalleryImages;

        return $this;
    }
}