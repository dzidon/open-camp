<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\RoleCreatedEvent;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RoleCreatedSubscriber
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: RoleCreatedEvent::NAME)]
    public function onCreatedSaveEntity(RoleCreatedEvent $event): void
    {
        $entity = $event->getRole();
        $this->repository->saveRole($entity, true);
    }
}