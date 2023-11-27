<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCompletedEvent;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserRegistrationCompletedSubscriber
{
    private UserRegistrationRepositoryInterface $userRegistrationRepository;

    private UserRepositoryInterface $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface             $userRepository,
                                EntityManagerInterface              $entityManager)
    {
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: UserRegistrationCompletedEvent::NAME)]
    public function onCompleteUpdate(UserRegistrationCompletedEvent $event): void
    {
        $result = $event->getUserRegistrationCompletionResult();
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();
        $flush = false;

        if ($user !== null)
        {
            $this->userRepository->saveUser($user, false);
            $flush = true;
        }

        if ($usedUserRegistration !== null)
        {
            $this->userRegistrationRepository->saveUserRegistration($usedUserRegistration, false);
            $flush = true;
        }

        foreach ($disabledUserRegistrations as $disabledUserRegistration)
        {
            $this->userRegistrationRepository->saveUserRegistration($disabledUserRegistration, false);
            $flush = true;
        }

        if ($flush)
        {
            $this->entityManager->flush();
        }
    }
}