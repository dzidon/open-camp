<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCompleteEvent;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Model\Service\UserRegistration\UserRegistererInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserRegistrationCompleteSubscriber
{
    private UserRegistererInterface $userRegisterer;

    private UserRegistrationRepositoryInterface $userRegistrationRepository;

    private UserRepositoryInterface $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(UserRegistererInterface             $userRegisterer,
                                UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface             $userRepository,
                                EntityManagerInterface              $entityManager)
    {
        $this->userRegisterer = $userRegisterer;
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: UserRegistrationCompleteEvent::NAME, priority: 200)]
    public function onCompleteUpdate(UserRegistrationCompleteEvent $event): void
    {
        $userRegistration = $event->getUserRegistration();
        $plainPasswordData = $event->getPlainPasswordData();
        $plainPassword = $plainPasswordData->getPlainPassword();
        $result = $this->userRegisterer->completeUserRegistration($userRegistration, $plainPassword);
        $event->setUserRegistrationCompletionResult($result);
    }

    #[AsEventListener(event: UserRegistrationCompleteEvent::NAME, priority: 100)]
    public function onCompleteSaveEntities(UserRegistrationCompleteEvent $event): void
    {
        $result = $event->getUserRegistrationCompletionResult();
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();
        $isFlush = $event->isFlush();
        $canFlush = false;

        if ($user !== null)
        {
            $this->userRepository->saveUser($user, false);
            $canFlush = true;
        }

        if ($usedUserRegistration !== null)
        {
            $this->userRegistrationRepository->saveUserRegistration($usedUserRegistration, false);
            $canFlush = true;
        }

        foreach ($disabledUserRegistrations as $disabledUserRegistration)
        {
            $this->userRegistrationRepository->saveUserRegistration($disabledUserRegistration, false);
            $canFlush = true;
        }

        if ($isFlush && $canFlush)
        {
            $this->entityManager->flush();
        }
    }
}