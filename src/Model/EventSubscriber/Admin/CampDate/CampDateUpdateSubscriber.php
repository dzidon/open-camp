<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Event\Admin\CampDate\CampDateUpdateEvent;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateUpdateSubscriber
{
    private CampDateRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CampDateRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDateUpdateEvent $event): void
    {
        $data = $event->getCampDateData();
        $entity = $event->getCampDate();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDateUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDateUpdateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $isFlush = $event->isFlush();
        $this->repository->saveCampDate($campDate, $isFlush);
    }
}