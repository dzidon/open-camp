<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\GalleryImageCategoryData;
use App\Model\Entity\GalleryImageCategory;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link GalleryImageCategoryData} to {@link GalleryImageCategory} and vice versa.
 */
class GalleryImageCategoryDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof GalleryImageCategoryData && $entity instanceof GalleryImageCategory;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var GalleryImageCategoryData $galleryImageCategoryData */
        /** @var GalleryImageCategory $galleryImageCategory */
        $galleryImageCategoryData = $data;
        $galleryImageCategory = $entity;

        $galleryImageCategoryData->setName($galleryImageCategory->getName());
        $galleryImageCategoryData->setUrlName($galleryImageCategory->getUrlName());
        $galleryImageCategoryData->setParent($galleryImageCategory->getParent());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var GalleryImageCategoryData $galleryImageCategoryData */
        /** @var GalleryImageCategory $galleryImageCategory */
        $galleryImageCategoryData = $data;
        $galleryImageCategory = $entity;

        $galleryImageCategory->setName($galleryImageCategoryData->getName());
        $galleryImageCategory->setUrlName($galleryImageCategoryData->getUrlName());
        $galleryImageCategory->setParent($galleryImageCategoryData->getParent());
    }
}