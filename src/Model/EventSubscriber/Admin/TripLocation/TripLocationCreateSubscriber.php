<?php

namespace App\Model\EventSubscriber\Admin\TripLocation;

use App\Model\Entity\TripLocation;
use App\Model\Event\Admin\TripLocation\TripLocationCreatedEvent;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TripLocationCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: TripLocationCreateEvent::NAME)]
    public function onCreateFillEntity(TripLocationCreateEvent $event): void
    {
        $data = $event->getTripLocationData();
        $entity = new TripLocation($data->getName(), $data->getPrice(), $data->getPriority(), $data->getTripLocationPath());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new TripLocationCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, TripLocationCreatedEvent::NAME);
    }
}