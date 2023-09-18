<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ProfileData;
use App\Model\Entity\User;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ProfileData} to {@link User} and vice versa.
 */
class ProfileDataTransfer implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ProfileData && $entity instanceof User;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var ProfileData $profileData */
        /** @var User $user */
        $profileData = $data;
        $user = $entity;

        $profileData->setNameFirst($user->getNameFirst());
        $profileData->setNameLast($user->getNameLast());
        $profileData->setLeaderPhoneNumber($user->getLeaderPhoneNumber());
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var ProfileData $profileData */
        /** @var User $user */
        $profileData = $data;
        $user = $entity;

        $user->setNameFirst($profileData->getNameFirst());
        $user->setNameLast($profileData->getNameLast());
        $user->setLeaderPhoneNumber($profileData->getLeaderPhoneNumber());
    }
}