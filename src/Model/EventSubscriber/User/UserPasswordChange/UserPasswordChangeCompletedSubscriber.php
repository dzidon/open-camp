<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompletedEvent;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserPasswordChangeCompletedSubscriber
{
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;

    private UserRepositoryInterface $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserRepositoryInterface               $userRepository,
                                EntityManagerInterface                $entityManager)
    {
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: UserPasswordChangeCompletedEvent::NAME)]
    public function onCompleteUpdate(UserPasswordChangeCompletedEvent $event): void
    {
        $result = $event->getUserPasswordChangeCompletionResult();
        $userPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();
        $flush = false;

        if ($userPasswordChange !== null)
        {
            $user = $userPasswordChange->getUser();

            if ($user !== null)
            {
                $this->userRepository->saveUser($user, false);
            }

            $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, false);
            $flush = true;
        }

        foreach ($disabledUserPasswordChanges as $disabledUserPasswordChange)
        {
            $this->userPasswordChangeRepository->saveUserPasswordChange($disabledUserPasswordChange, false);
            $flush = true;
        }

        if ($flush)
        {
            $this->entityManager->flush();
        }
    }
}