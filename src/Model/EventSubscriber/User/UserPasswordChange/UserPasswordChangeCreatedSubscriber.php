<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreatedEvent;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Service\Mailer\UserPasswordChangeMailerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserPasswordChangeCreatedSubscriber
{
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;

    private UserPasswordChangeMailerInterface $mailer;

    public function __construct(UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserPasswordChangeMailerInterface     $mailer)
    {
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->mailer = $mailer;
    }

    #[AsEventListener(event: UserPasswordChangeCreatedEvent::NAME, priority: 200)]
    public function onCreatedSendEmail(UserPasswordChangeCreatedEvent $event): void
    {
        $result = $event->getUserPasswordChangeResult();
        $userPasswordChange = $result->getUserPasswordChange();
        $token = $result->getToken();
        $isFake = $result->isFake();

        $expireAt = $userPasswordChange->getExpireAt();
        $user = $userPasswordChange->getUser();
        $email = ($user === null ? 'fake@email.com' : $user->getEmail());

        $this->mailer->sendEmail($email, $token, $expireAt, $isFake);
    }

    #[AsEventListener(event: UserPasswordChangeCreatedEvent::NAME, priority: 100)]
    public function onCreatedSave(UserPasswordChangeCreatedEvent $event): void
    {
        $result = $event->getUserPasswordChangeResult();

        if ($result->isFake())
        {
            return;
        }

        $userPasswordChange = $result->getUserPasswordChange();
        $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, true);
    }
}