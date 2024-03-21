<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCreateEvent;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Service\UserRegistration\UserRegistrationFactoryInterface;
use App\Model\Service\UserRegistration\UserRegistrationMailerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserRegistrationCreateSubscriber
{
    private UserRegistrationFactoryInterface $userRegistrationFactory;

    private UserRegistrationRepositoryInterface $userRegistrationRepository;

    private UserRegistrationMailerInterface $mailer;

    public function __construct(UserRegistrationFactoryInterface    $userRegistrationFactory,
                                UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRegistrationMailerInterface     $mailer)
    {
        $this->userRegistrationFactory = $userRegistrationFactory;
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->mailer = $mailer;
    }

    #[AsEventListener(event: UserRegistrationCreateEvent::NAME, priority: 300)]
    public function onCreateDispatchResult(UserRegistrationCreateEvent $event): void
    {
        $registrationData = $event->getRegistrationData();
        $email = $registrationData->getEmail();
        $result = $this->userRegistrationFactory->createUserRegistration($email);
        $event->setUserRegistrationResult($result);
    }

    #[AsEventListener(event: UserRegistrationCreateEvent::NAME, priority: 200)]
    public function onCreateSendEmail(UserRegistrationCreateEvent $event): void
    {
        $result = $event->getUserRegistrationResult();
        $token = $result->getToken();
        $isFake = $result->isFake();

        $userRegistration = $result->getUserRegistration();
        $email = $userRegistration->getEmail();
        $expireAt = $userRegistration->getExpireAt();

        $this->mailer->sendEmail($email, $token, $expireAt, $isFake);
    }

    #[AsEventListener(event: UserRegistrationCreateEvent::NAME, priority: 100)]
    public function onCreateSave(UserRegistrationCreateEvent $event): void
    {
        $result = $event->getUserRegistrationResult();

        if ($result->isFake())
        {
            return;
        }

        $userRegistration = $result->getUserRegistration();
        $isFlush = $event->isFlush();
        $this->userRegistrationRepository->saveUserRegistration($userRegistration, $isFlush);
    }
}