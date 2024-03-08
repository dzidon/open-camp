<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDate\CampDateCreateEvent;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateCreateSubscriber
{
    private CampDateRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampDateCreateEvent $event): void
    {
        $data = $event->getCampDateData();
        $entity = new CampDate(
            $data->getStartAt(),
            $data->getEndAt(),
            $data->getDeposit(),
            $data->getPriceWithoutDeposit(),
            $data->getCapacity(),
            $data->getCamp()
        );

        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampDate($entity);
    }

    #[AsEventListener(event: CampDateCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampDateCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $isFlush = $event->isFlush();
        $this->repository->saveCampDate($campDate, $isFlush);
    }
}