<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\SuperAdminRoleInitializedEvent;
use App\Model\Event\Admin\Role\SuperAdminRoleInitializeEvent;
use App\Model\Service\Role\SuperAdminRoleInitializerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SuperAdminRoleInitializeSubscriber
{
    private SuperAdminRoleInitializerInterface $superAdminRoleInitializer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(SuperAdminRoleInitializerInterface $superAdminRoleInitializer,
                                EventDispatcherInterface           $eventDispatcher)
    {
        $this->superAdminRoleInitializer = $superAdminRoleInitializer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: SuperAdminRoleInitializeEvent::NAME)]
    public function onInitializeDispatch(): void
    {
        $role = $this->superAdminRoleInitializer->initializeSuperAdminRole();

        $event = new SuperAdminRoleInitializedEvent($role);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}