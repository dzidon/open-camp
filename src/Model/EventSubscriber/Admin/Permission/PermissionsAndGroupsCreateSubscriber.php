<?php

namespace App\Model\EventSubscriber\Admin\Permission;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreatedEvent;
use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreateEvent;
use App\Model\Service\Permission\PermissionsAndGroupsFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PermissionsAndGroupsCreateSubscriber
{
    private PermissionsAndGroupsFactoryInterface $permissionsAndGroupsFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(PermissionsAndGroupsFactoryInterface $permissionsAndGroupsFactory,
                                EventDispatcherInterface             $eventDispatcher)
    {
        $this->permissionsAndGroupsFactory = $permissionsAndGroupsFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: PermissionsAndGroupsCreateEvent::NAME)]
    public function onCreateDispatch(): void
    {
        $result = $this->permissionsAndGroupsFactory->createPermissionsAndGroups();

        $event = new PermissionsAndGroupsCreatedEvent($result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}