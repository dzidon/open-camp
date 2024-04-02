<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationCamperData;
use App\Model\Entity\ApplicationTripLocationPath;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationCamperData} to {@link ApplicationTripLocationPath} and vice versa.
 */
class ApplicationTripLocationPathDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationCamperData && $entity instanceof ApplicationTripLocationPath;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationCamperData $applicationCamperData */
        /** @var ApplicationTripLocationPath $applicationTripLocationPath */
        $applicationCamperData = $data;
        $applicationTripLocationPath = $entity;

        if ($applicationTripLocationPath->isThere())
        {
            $applicationCamperData->setTripLocationThere($applicationTripLocationPath->getLocation());
        }
        else
        {
            $applicationCamperData->setTripLocationBack($applicationTripLocationPath->getLocation());
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationCamperData $applicationCamperData */
        /** @var ApplicationTripLocationPath $applicationTripLocationPath */
        $applicationCamperData = $data;
        $applicationTripLocationPath = $entity;

        if ($applicationTripLocationPath->isThere())
        {
            $applicationTripLocationPath->setLocation($applicationCamperData->getTripLocationThere());
        }
        else
        {
            $applicationTripLocationPath->setLocation($applicationCamperData->getTripLocationBack());
        }
    }
}