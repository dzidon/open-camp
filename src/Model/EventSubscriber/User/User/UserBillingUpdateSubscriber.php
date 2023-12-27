<?php

namespace App\Model\EventSubscriber\User\User;

use App\Model\Event\User\User\UserBillingUpdateEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserBillingUpdateSubscriber
{
    private UserRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(UserRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: UserBillingUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(UserBillingUpdateEvent $event): void
    {
        $data = $event->getBillingData();
        $entity = $event->getUser();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: UserBillingUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(UserBillingUpdateEvent $event): void
    {
        $entity = $event->getUser();
        $isFlush = $event->isFlush();
        $this->repository->saveUser($entity, $isFlush);
    }
}