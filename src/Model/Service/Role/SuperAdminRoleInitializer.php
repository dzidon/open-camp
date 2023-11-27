<?php

namespace App\Model\Service\Role;

use App\Model\Entity\Role;
use App\Model\Repository\PermissionRepositoryInterface;
use App\Model\Repository\RoleRepositoryInterface;

/**
 * @inheritDoc
 */
class SuperAdminRoleInitializer implements SuperAdminRoleInitializerInterface
{
    private PermissionRepositoryInterface $permissionRepository;

    private RoleRepositoryInterface $roleRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @inheritDoc
     */
    public function initializeSuperAdminRole(): Role
    {
        $permissions = $this->permissionRepository->findAll();
        $role = $this->roleRepository->findOneByLabel('Super admin');

        if ($role === null)
        {
            $role = new Role('Super admin');
        }

        foreach ($permissions as $permission)
        {
            $role->addPermission($permission);
        }

        return $role;
    }
}