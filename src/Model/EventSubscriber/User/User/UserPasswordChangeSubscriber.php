<?php

namespace App\Model\EventSubscriber\User\User;

use App\Model\Event\User\User\UserPasswordChangeEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordChangeSubscriber
{
    private UserRepositoryInterface $repository;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepositoryInterface $repository, UserPasswordHasherInterface $hasher)
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    #[AsEventListener(event: UserPasswordChangeEvent::NAME, priority: 200)]
    public function onChangeSetHash(UserPasswordChangeEvent $event): void
    {
        $data = $event->getProfilePasswordChangeData();
        $user = $event->getUser();

        $newPasswordChangeData = $data->getNewPasswordData();
        $newPlainPassword = $newPasswordChangeData->getPlainPassword();
        $newPasswordHash = $this->hasher->hashPassword($user, $newPlainPassword);
        $user->setPassword($newPasswordHash);
    }

    #[AsEventListener(event: UserPasswordChangeEvent::NAME, priority: 100)]
    public function onChangeSaveEntity(UserPasswordChangeEvent $event): void
    {
        $entity = $event->getUser();
        $this->repository->saveUser($entity, true);
    }
}