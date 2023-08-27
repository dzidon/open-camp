<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampImageData;
use App\Model\Entity\CampImage;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampImageData} to {@link CampImage} and vice versa.
 */
class CampImageDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampImageData && $entity instanceof CampImage;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampImageData $campImageData */
        /** @var CampImage $campImage */
        $campImageData = $data;
        $campImage = $entity;

        $campImageData->setPriority($campImage->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampImageData $campImageData */
        /** @var CampImage $campImage */
        $campImageData = $data;
        $campImage = $entity;

        $campImage->setPriority($campImageData->getPriority());
    }
}