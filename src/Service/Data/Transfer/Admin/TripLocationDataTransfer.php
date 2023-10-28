<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link TripLocationData} to {@link TripLocation} and vice versa.
 */
class TripLocationDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof TripLocationData && $entity instanceof TripLocation;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var TripLocationData $tripLocationData */
        /** @var TripLocation $tripLocation */
        $tripLocationData = $data;
        $tripLocation = $entity;

        $tripLocationData->setName($tripLocation->getName());
        $tripLocationData->setPrice($tripLocation->getPrice());
        $tripLocationData->setPriority($tripLocation->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var TripLocationData $tripLocationData */
        /** @var TripLocation $tripLocation */
        $tripLocationData = $data;
        $tripLocation = $entity;

        $tripLocation->setName($tripLocationData->getName());
        $tripLocation->setPrice($tripLocationData->getPrice());
        $tripLocation->setPriority($tripLocationData->getPriority());
    }
}