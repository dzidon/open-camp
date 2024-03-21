<?php

namespace App\Model\EventSubscriber\User\User;

use App\Model\Entity\User;
use App\Model\Event\User\User\UserSocialLoginCreateEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserSocialLoginCreateSubscriber
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: UserSocialLoginCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntity(UserSocialLoginCreateEvent $event): void
    {
        $email = $event->getEmail();
        $user = new User($email);
        $event->setUser($user);
    }

    #[AsEventListener(event: UserSocialLoginCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(UserSocialLoginCreateEvent $event): void
    {
        $entity = $event->getUser();
        $isFlush = $event->isFlush();
        $this->repository->saveUser($entity, $isFlush);
    }
}