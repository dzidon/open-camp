<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\UserCreatedEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserCreatedSubscriber
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: UserCreatedEvent::NAME)]
    public function onCreatedSaveEntity(UserCreatedEvent $event): void
    {
        $entity = $event->getUser();
        $this->repository->saveUser($entity, true);
    }
}