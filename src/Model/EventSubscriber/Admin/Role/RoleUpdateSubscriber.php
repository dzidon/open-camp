<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\RoleUpdateEvent;
use App\Model\Repository\RoleRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RoleUpdateSubscriber
{
    private RoleRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(RoleRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: RoleUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(RoleUpdateEvent $event): void
    {
        $data = $event->getRoleData();
        $entity = $event->getRole();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: RoleUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(RoleUpdateEvent $event): void
    {
        $entity = $event->getRole();
        $isFlush = $event->isFlush();
        $this->repository->saveRole($entity, $isFlush);
    }
}