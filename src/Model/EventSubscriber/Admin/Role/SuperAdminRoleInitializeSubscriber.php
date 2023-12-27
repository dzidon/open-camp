<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\SuperAdminRoleInitializeEvent;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Service\Role\SuperAdminRoleInitializerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class SuperAdminRoleInitializeSubscriber
{
    private SuperAdminRoleInitializerInterface $superAdminRoleInitializer;

    private RoleRepositoryInterface $repository;

    public function __construct(SuperAdminRoleInitializerInterface $superAdminRoleInitializer,
                                RoleRepositoryInterface            $repository)
    {
        $this->superAdminRoleInitializer = $superAdminRoleInitializer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: SuperAdminRoleInitializeEvent::NAME, priority: 200)]
    public function onInitializeDispatch(SuperAdminRoleInitializeEvent $event): void
    {
        $role = $this->superAdminRoleInitializer->initializeSuperAdminRole();
        $event->setRole($role);
    }

    #[AsEventListener(event: SuperAdminRoleInitializeEvent::NAME, priority: 100)]
    public function onInitializedSaveRole(SuperAdminRoleInitializeEvent $event): void
    {
        $role = $event->getRole();
        $isFlush = $event->isFlush();
        $this->repository->saveRole($role, $isFlush);
    }
}