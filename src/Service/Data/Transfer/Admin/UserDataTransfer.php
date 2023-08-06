<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\UserData;
use App\Model\Entity\User;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Transfers data from {@link UserData} to {@link User} and vice versa.
 */
class UserDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $transferRegistry;
    private Security $security;

    public function __construct(DataTransferRegistryInterface $transferRegistry, Security $security)
    {
        $this->transferRegistry = $transferRegistry;
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

        $billingData = $userData->getBillingData();
        $this->transferRegistry->fillData($billingData, $user);
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

            $billingData = $userData->getBillingData();
            $this->transferRegistry->fillEntity($billingData, $user);
        }

        if ($this->security->isGranted('user_update_role'))
        {
            $user->setRole($userData->getRole());
        }
    }
}