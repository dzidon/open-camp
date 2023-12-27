<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\RoleDeleteEvent;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RoleDeleteSubscriber
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: RoleDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(RoleDeleteEvent $event): void
    {
        $entity = $event->getRole();
        $isFlush = $event->isFlush();
        $this->repository->removeRole($entity, $isFlush);
    }
}