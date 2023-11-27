<?php

namespace App\Model\EventSubscriber\Admin\CampCategory;

use App\Model\Entity\CampCategory;
use App\Model\Event\Admin\CampCategory\CampCategoryCreatedEvent;
use App\Model\Event\Admin\CampCategory\CampCategoryCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampCategoryCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: CampCategoryCreateEvent::NAME)]
    public function onCreateFillEntity(CampCategoryCreateEvent $event): void
    {
        $data = $event->getCampCategoryData();
        $entity = new CampCategory($data->getName(), $data->getUrlName());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new CampCategoryCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, CampCategoryCreatedEvent::NAME);
    }
}