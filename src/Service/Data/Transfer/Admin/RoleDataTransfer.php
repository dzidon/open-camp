<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Role;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Transfers data from {@link RoleData} to {@link Role} and vice versa.
 */
class RoleDataTransfer implements DataTransferInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof RoleData && $entity instanceof Role;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var RoleData $roleData */
        /** @var Role $role */
        $roleData = $data;
        $role = $entity;

        $roleData->setLabel($role->getLabel());
        $this->propertyAccessor->setValue($roleData, 'permissions', $role->getPermissions());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var RoleData $roleData */
        /** @var Role $role */
        $roleData = $data;
        $role = $entity;

        $role->setLabel($roleData->getLabel());
        $this->propertyAccessor->setValue($role, 'permissions', $roleData->getPermissions());
    }
}