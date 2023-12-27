<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\RoleCreateEvent;
use App\Model\Repository\RoleRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RoleCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private RoleRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, RoleRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: RoleCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(RoleCreateEvent $event): void
    {
        $data = $event->getRoleData();
        $entity = new Role($data->getLabel());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setRole($entity);
    }

    #[AsEventListener(event: RoleCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(RoleCreateEvent $event): void
    {
        $entity = $event->getRole();
        $isFlush = $event->isFlush();
        $this->repository->saveRole($entity, $isFlush);
    }
}