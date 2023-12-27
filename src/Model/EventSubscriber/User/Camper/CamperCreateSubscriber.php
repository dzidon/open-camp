<?php

namespace App\Model\EventSubscriber\User\Camper;

use App\Model\Entity\Camper;
use App\Model\Event\User\Camper\CamperCreateEvent;
use App\Model\Repository\CamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CamperCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private CamperRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CamperRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CamperCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CamperCreateEvent $event): void
    {
        $data = $event->getCamperData();
        $user = $event->getUser();
        $entity = new Camper($data->getNameFirst(), $data->getNameLast(), $data->getGender(), $data->getBornAt(), $user);
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCamper($entity);
    }

    #[AsEventListener(event: CamperCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CamperCreateEvent $event): void
    {
        $entity = $event->getCamper();
        $isFlush = $event->isFlush();
        $this->repository->saveCamper($entity, $isFlush);
    }
}