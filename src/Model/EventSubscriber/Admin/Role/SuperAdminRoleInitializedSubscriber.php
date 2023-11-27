<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\SuperAdminRoleInitializedEvent;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class SuperAdminRoleInitializedSubscriber
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    #[AsEventListener(event: SuperAdminRoleInitializedEvent::NAME)]
    public function onInitializedSaveRole(SuperAdminRoleInitializedEvent $event): void
    {
        $role = $event->getRole();

        $this->roleRepository->saveRole($role, true);
    }
}