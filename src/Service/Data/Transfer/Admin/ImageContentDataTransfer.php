<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ImageContentData;
use App\Model\Entity\ImageContent;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ImageContentData} to {@link ImageContent} and vice versa.
 */
class ImageContentDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ImageContentData && $entity instanceof ImageContent;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ImageContentData $imageContentData */
        /** @var ImageContent $imageContent */
        $imageContentData = $data;
        $imageContent = $entity;

        $imageContentData->setUrl($imageContent->getUrl());
        $imageContentData->setAlt($imageContent->getAlt());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ImageContentData $imageContentData */
        /** @var ImageContent $imageContent */
        $imageContentData = $data;
        $imageContent = $entity;

        $imageContent->setUrl($imageContentData->getUrl());
        $imageContent->setAlt($imageContentData->getAlt());
    }
}