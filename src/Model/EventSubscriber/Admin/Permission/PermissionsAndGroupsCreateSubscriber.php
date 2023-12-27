<?php

namespace App\Model\EventSubscriber\Admin\Permission;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreateEvent;
use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepositoryInterface;
use App\Model\Service\Permission\PermissionsAndGroupsFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PermissionsAndGroupsCreateSubscriber
{
    private PermissionsAndGroupsFactoryInterface $permissionsAndGroupsFactory;

    private PermissionRepositoryInterface $permissionRepository;

    private PermissionGroupRepositoryInterface $permissionGroupRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(PermissionsAndGroupsFactoryInterface $permissionsAndGroupsFactory,
                                PermissionRepositoryInterface        $permissionRepository,
                                PermissionGroupRepositoryInterface   $permissionGroupRepository,
                                EntityManagerInterface               $entityManager)
    {
        $this->permissionsAndGroupsFactory = $permissionsAndGroupsFactory;
        $this->permissionRepository = $permissionRepository;
        $this->permissionGroupRepository = $permissionGroupRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: PermissionsAndGroupsCreateEvent::NAME, priority: 200)]
    public function onCreateDispatch(PermissionsAndGroupsCreateEvent $event): void
    {
        $result = $this->permissionsAndGroupsFactory->createPermissionsAndGroups();
        $event->setPermissionsAndGroupsCreationResult($result);
    }

    #[AsEventListener(event: PermissionsAndGroupsCreateEvent::NAME, priority: 100)]
    public function onCreateSavePermissionsAndGroups(PermissionsAndGroupsCreateEvent $event): void
    {
        $result = $event->getPermissionsAndGroupsCreationResult();
        $permissions = $result->getCreatedPermissions();
        $permissionGroups = $result->getCreatedPermissionGroups();
        $isFlush = $event->isFlush();

        foreach ($permissionGroups as $permissionGroup)
        {
            $this->permissionGroupRepository->savePermissionGroup($permissionGroup, false);
        }

        foreach ($permissions as $permission)
        {
            $this->permissionRepository->savePermission($permission, false);
        }

        if ($isFlush && (!empty($permissionGroups) || !empty($permissions)))
        {
            $this->entityManager->flush();
        }
    }
}