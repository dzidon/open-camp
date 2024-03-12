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
        $campDateUserData->setCanManageApplicationPayments($campDateUser->canManageApplicationPayments());
        $campDateUserData->setCanManageApplications($campDateUser->canManageApplications());
        $campDateUserData->setCanUpdateApplicationsState($campDateUser->canUpdateApplicationsState());
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
        $campDateUser->setCanManageApplicationPayments($campDateUserData->canManageApplicationPayments());
        $campDateUser->setCanManageApplications($campDateUserData->canManageApplications());
        $campDateUser->setCanUpdateApplicationsState($campDateUserData->canUpdateApplicationsState());
    }
}