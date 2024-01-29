<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateUserData;
use App\Model\Entity\CampDateUser;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampDateUserData} to {@link CampDateUser} and vice versa.
 */
class CampDateUserDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampDateUserData && $entity instanceof CampDateUser;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampDateUserData $campDateUserData */
        /** @var CampDateUser $campDateUser */
        $campDateUserData = $data;
        $campDateUser = $entity;

        $campDateUserData->setUser($campDateUser->getUser());
        $campDateUserData->setCanUpdateApplicationPayments($campDateUser->canUpdateApplicationPayments());
        $campDateUserData->setCanUpdateApplications($campDateUser->canUpdateApplications());
        $campDateUserData->setCanUpdateApplicationsState($campDateUser->canUpdateApplicationsState());
        $campDateUserData->setPriority($campDateUser->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampDateUserData $campDateUserData */
        /** @var CampDateUser $campDateUser */
        $campDateUserData = $data;
        $campDateUser = $entity;

        $campDateUser->setUser($campDateUserData->getUser());
        $campDateUser->setCanUpdateApplicationPayments($campDateUserData->canUpdateApplicationPayments());
        $campDateUser->setCanUpdateApplications($campDateUserData->canUpdateApplications());
        $campDateUser->setCanUpdateApplicationsState($campDateUserData->canUpdateApplicationsState());
        $campDateUser->setPriority($campDateUserData->getPriority());
    }
}