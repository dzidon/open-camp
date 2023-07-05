<?php

namespace App\Form\DataTransfer\Transfer\Admin;

use App\Entity\User;
use App\Form\DataTransfer\Data\Admin\UserData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Transfers data from {@link UserData} to {@link User} and vice versa.
 */
class UserDataTransfer implements DataTransferInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof UserData && $entity instanceof User;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var UserData $userData */
        /** @var User $user */
        $userData = $data;
        $user = $entity;

        $userData->setId($user->getId());
        $userData->setEmail($user->getEmail());
        $userData->setRole($user->getRole());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var UserData $userData */
        /** @var User $user */
        $userData = $data;
        $user = $entity;

        if ($this->security->isGranted('user_update'))
        {
            $user->setEmail($userData->getEmail());
        }

        if ($this->security->isGranted('user_update_role'))
        {
            $user->setRole($userData->getRole());
        }
    }
}