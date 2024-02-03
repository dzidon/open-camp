<?php

namespace App\Model\EventSubscriber\Admin\CampDateUser;

use App\Model\Entity\CampDateUser;
use App\Model\Event\Admin\CampDateUser\CampDateUserCreateEvent;
use App\Model\Repository\CampDateUserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateUserCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private CampDateUserRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CampDateUserRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateUserCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampDateUserCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $data = $event->getCampDateUserData();
        $entity = new CampDateUser($campDate, $data->getUser());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampDateUser($entity);
    }

    #[AsEventListener(event: CampDateUserCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampDateUserCreateEvent $event): void
    {
        $entity = $event->getCampDateUser();
        $flush = $event->isFlush();
        $this->repository->saveCampDateUser($entity, $flush);
    }
}