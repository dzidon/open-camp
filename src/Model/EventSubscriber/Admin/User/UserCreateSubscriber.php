<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserCreateEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private UserRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, UserRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: UserCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(UserCreateEvent $event): void
    {
        $data = $event->getUserData();
        $entity = new User($data->getEmail());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setUser($entity);
    }

    #[AsEventListener(event: UserCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(UserCreateEvent $event): void
    {
        $entity = $event->getUser();
        $isFlush = $event->isFlush();
        $this->repository->saveUser($entity, $isFlush);
    }
}