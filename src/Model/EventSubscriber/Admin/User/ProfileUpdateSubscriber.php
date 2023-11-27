<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\ProfileUpdateEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ProfileUpdateSubscriber
{
    private UserRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(UserRepositoryInterface       $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ProfileUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ProfileUpdateEvent $event): void
    {
        $data = $event->getProfileData();
        $entity = $event->getUser();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ProfileUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ProfileUpdateEvent $event): void
    {
        $entity = $event->getUser();
        $this->repository->saveUser($entity, true);
    }
}