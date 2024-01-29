<?php

namespace App\Model\EventSubscriber\Admin\CampDateUser;

use App\Model\Event\Admin\CampDateUser\CampDateUserUpdateEvent;
use App\Model\Repository\CampDateUserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateUserUpdateSubscriber
{
    private CampDateUserRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateUserRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateUserUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDateUserUpdateEvent $event): void
    {
        $data = $event->getCampDateUserData();
        $entity = $event->getCampDateUser();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDateUserUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDateUserUpdateEvent $event): void
    {
        $entity = $event->getCampDateUser();
        $flush = $event->isFlush();
        $this->repository->saveCampDateUser($entity, $flush);
    }
}