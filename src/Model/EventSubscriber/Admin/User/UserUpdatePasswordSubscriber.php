<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\UserUpdatePasswordEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdatePasswordSubscriber
{
    private UserRepositoryInterface $repository;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepositoryInterface $repository, UserPasswordHasherInterface $hasher)
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    #[AsEventListener(event: UserUpdatePasswordEvent::NAME, priority: 200)]
    public function onUpdateSetPassword(UserUpdatePasswordEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getPlainPasswordData();
        $plainPassword = $data->getPlainPassword();

        if ($plainPassword === null)
        {
            $user->setPassword(null);
        }
        else
        {
            $password = $this->hasher->hashPassword($user, $plainPassword);
            $user->setPassword($password);
        }
    }

    #[AsEventListener(event: UserUpdatePasswordEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(UserUpdatePasswordEvent $event): void
    {
        $entity = $event->getUser();
        $this->repository->saveUser($entity, true);
    }
}