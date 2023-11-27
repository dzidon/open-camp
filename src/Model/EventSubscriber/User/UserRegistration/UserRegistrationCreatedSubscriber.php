<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCreatedEvent;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Service\Mailer\UserRegistrationMailerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserRegistrationCreatedSubscriber
{
    private UserRegistrationRepositoryInterface $userRegistrationRepository;

    private UserRegistrationMailerInterface $mailer;

    public function __construct(UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRegistrationMailerInterface     $mailer)
    {
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->mailer = $mailer;
    }

    #[AsEventListener(event: UserRegistrationCreatedEvent::NAME, priority: 200)]
    public function onCreatedSendEmail(UserRegistrationCreatedEvent $event): void
    {
        $result = $event->getUserRegistrationResult();
        $token = $result->getToken();
        $isFake = $result->isFake();

        $userRegistration = $result->getUserRegistration();
        $email = $userRegistration->getEmail();
        $expireAt = $userRegistration->getExpireAt();

        $this->mailer->sendEmail($email, $token, $expireAt, $isFake);
    }

    #[AsEventListener(event: UserRegistrationCreatedEvent::NAME, priority: 100)]
    public function onCreatedSave(UserRegistrationCreatedEvent $event): void
    {
        $result = $event->getUserRegistrationResult();

        if ($result->isFake())
        {
            return;
        }

        $userRegistration = $result->getUserRegistration();
        $this->userRegistrationRepository->saveUserRegistration($userRegistration, true);
    }
}