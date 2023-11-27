<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\RoleCreatedEvent;
use App\Model\Event\Admin\Role\RoleCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoleCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: RoleCreateEvent::NAME)]
    public function onCreateFillEntity(RoleCreateEvent $event): void
    {
        $data = $event->getRoleData();
        $entity = new Role($data->getLabel());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new RoleCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, RoleCreatedEvent::NAME);
    }
}