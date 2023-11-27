<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\UserDeleteEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserDeleteSubscriber
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: UserDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(UserDeleteEvent $event): void
    {
        $entity = $event->getUser();
        $this->repository->removeUser($entity, true);
    }
}