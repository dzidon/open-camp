<?php

namespace App\Model\EventSubscriber\Admin\Permission;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreatedEvent;
use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PermissionsAndGroupsCreatedSubscriber
{
    private PermissionRepositoryInterface $permissionRepository;

    private PermissionGroupRepositoryInterface $permissionGroupRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(PermissionRepositoryInterface      $permissionRepository,
                                PermissionGroupRepositoryInterface $permissionGroupRepository,
                                EntityManagerInterface             $entityManager)
    {
        $this->permissionRepository = $permissionRepository;
        $this->permissionGroupRepository = $permissionGroupRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: PermissionsAndGroupsCreatedEvent::NAME)]
    public function onCreatedSavePermissionsAndGroups(PermissionsAndGroupsCreatedEvent $event): void
    {
        $result = $event->getPermissionsAndGroupsCreationResult();
        $permissions = $result->getCreatedPermissions();
        $permissionGroups = $result->getCreatedPermissionGroups();
        $flush = false;

        foreach ($permissionGroups as $permissionGroup)
        {
            $this->permissionGroupRepository->savePermissionGroup($permissionGroup, false);
            $flush = true;
        }

        foreach ($permissions as $permission)
        {
            $this->permissionRepository->savePermission($permission, false);
            $flush = true;
        }

        if ($flush)
        {
            $this->entityManager->flush();
        }
    }
}