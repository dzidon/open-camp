<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\UserDeleteEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Model\Service\User\UserImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserDeleteSubscriber
{
    private UserRepositoryInterface $repository;

    private UserImageFilesystemInterface $userImageFilesystem;

    public function __construct(UserRepositoryInterface $repository, UserImageFilesystemInterface $userImageFilesystem)
    {
        $this->repository = $repository;
        $this->userImageFilesystem = $userImageFilesystem;
    }

    #[AsEventListener(event: UserDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveFile(UserDeleteEvent $event): void
    {
        $entity = $event->getUser();
        $this->userImageFilesystem->removeImageFile($entity);
    }

    #[AsEventListener(event: UserDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(UserDeleteEvent $event): void
    {
        $entity = $event->getUser();
        $isFlush = $event->isFlush();
        $this->repository->removeUser($entity, $isFlush);
    }
}