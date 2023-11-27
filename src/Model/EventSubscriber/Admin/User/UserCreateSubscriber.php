<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserCreatedEvent;
use App\Model\Event\Admin\User\UserCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: UserCreateEvent::NAME)]
    public function onCreateFillEntity(UserCreateEvent $event): void
    {
        $data = $event->getUserData();
        $entity = new User($data->getEmail());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new UserCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, UserCreatedEvent::NAME);
    }
}