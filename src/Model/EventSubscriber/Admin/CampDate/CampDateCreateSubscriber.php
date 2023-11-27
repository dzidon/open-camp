<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDate\CampDateCreatedEvent;
use App\Model\Event\Admin\CampDate\CampDateCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampDateCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: CampDateCreateEvent::NAME)]
    public function onCreateFillEntity(CampDateCreateEvent $event): void
    {
        $data = $event->getCampDateData();
        $entity = new CampDate($data->getStartAt(), $data->getEndAt(), $data->getPrice(), $data->getCapacity(), $data->getCamp());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new CampDateCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, CampDateCreatedEvent::NAME);
    }
}