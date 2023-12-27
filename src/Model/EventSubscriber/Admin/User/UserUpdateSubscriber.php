<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\UserUpdateEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserUpdateSubscriber
{
    private UserRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(UserRepositoryInterface $repository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: UserUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(UserUpdateEvent $event): void
    {
        $data = $event->getUserData();
        $entity = $event->getUser();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: UserUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(UserUpdateEvent $event): void
    {
        $entity = $event->getUser();
        $isFlush = $event->isFlush();
        $this->repository->saveUser($entity, $isFlush);
    }
}