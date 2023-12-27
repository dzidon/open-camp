<?php

namespace App\Model\EventSubscriber\User\Camper;

use App\Model\Event\User\Camper\CamperUpdateEvent;
use App\Model\Repository\CamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CamperUpdateSubscriber
{
    private CamperRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CamperRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CamperUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CamperUpdateEvent $event): void
    {
        $data = $event->getCamperData();
        $entity = $event->getCamper();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CamperUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CamperUpdateEvent $event): void
    {
        $entity = $event->getCamper();
        $isFlush = $event->isFlush();
        $this->repository->saveCamper($entity, $isFlush);
    }
}