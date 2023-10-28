<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Entity\TripLocationPath;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link TripLocationPathData} to {@link TripLocationPath} and vice versa.
 */
class TripLocationPathDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof TripLocationPathData && $entity instanceof TripLocationPath;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var TripLocationPathData $tripLocationPathData */
        /** @var TripLocationPath $tripLocationPath */
        $tripLocationPathData = $data;
        $tripLocationPath = $entity;

        $tripLocationPathData->setName($tripLocationPath->getName());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var TripLocationPathData $tripLocationPathData */
        /** @var TripLocationPath $tripLocationPath */
        $tripLocationPathData = $data;
        $tripLocationPath = $entity;

        $tripLocationPath->setName($tripLocationPathData->getName());
    }
}