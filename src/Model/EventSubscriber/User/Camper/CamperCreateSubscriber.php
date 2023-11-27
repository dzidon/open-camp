<?php

namespace App\Model\EventSubscriber\User\Camper;

use App\Model\Entity\Camper;
use App\Model\Event\User\Camper\CamperCreatedEvent;
use App\Model\Event\User\Camper\CamperCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CamperCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: CamperCreateEvent::NAME)]
    public function onCreateFillEntity(CamperCreateEvent $event): void
    {
        $data = $event->getCamperData();
        $user = $event->getUser();
        $entity = new Camper($data->getNameFirst(), $data->getNameLast(), $data->getGender(), $data->getBornAt(), $user);
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new CamperCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, CamperCreatedEvent::NAME);
    }
}