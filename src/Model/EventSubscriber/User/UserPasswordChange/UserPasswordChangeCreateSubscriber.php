<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Service\UserPasswordChange\UserPasswordChangeFactoryInterface;
use App\Model\Service\UserPasswordChange\UserPasswordChangeMailerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserPasswordChangeCreateSubscriber
{
    private UserPasswordChangeFactoryInterface $passwordChangeFactory;

    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;

    private UserPasswordChangeMailerInterface $mailer;

    public function __construct(UserPasswordChangeFactoryInterface    $passwordChangeFactory,
                                UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserPasswordChangeMailerInterface     $mailer)
    {
        $this->passwordChangeFactory = $passwordChangeFactory;
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->mailer = $mailer;
    }

    #[AsEventListener(event: UserPasswordChangeCreateEvent::NAME, priority: 300)]
    public function onCreateInstantiate(UserPasswordChangeCreateEvent $event): void
    {
        $passwordChangeData = $event->getPasswordChangeData();
        $email = $passwordChangeData->getEmail();
        $result = $this->passwordChangeFactory->createUserPasswordChange($email);
        $event->setUserPasswordChangeResult($result);
    }

    #[AsEventListener(event: UserPasswordChangeCreateEvent::NAME, priority: 200)]
    public function onCreateSendEmail(UserPasswordChangeCreateEvent $event): void
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

    #[AsEventListener(event: UserPasswordChangeCreateEvent::NAME, priority: 100)]
    public function onCreateSave(UserPasswordChangeCreateEvent $event): void
    {
        $result = $event->getUserPasswordChangeResult();

        if ($result->isFake())
        {
            return;
        }

        $userPasswordChange = $result->getUserPasswordChange();
        $isFlush = $event->isFlush();
        $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, $isFlush);
    }
}