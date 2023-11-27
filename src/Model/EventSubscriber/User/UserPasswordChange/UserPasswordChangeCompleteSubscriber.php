<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompletedEvent;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompleteEvent;
use App\Model\Service\UserPasswordChange\UserPasswordChangerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserPasswordChangeCompleteSubscriber
{
    private UserPasswordChangerInterface $userPasswordChanger;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UserPasswordChangerInterface $userPasswordChanger, EventDispatcherInterface $eventDispatcher)
    {
        $this->userPasswordChanger = $userPasswordChanger;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: UserPasswordChangeCompleteEvent::NAME)]
    public function onCompleteUpdate(UserPasswordChangeCompleteEvent $event): void
    {
        $userPasswordChange = $event->getUserPasswordChange();
        $passwordData = $event->getPlainPasswordData();
        $plainPassword = $passwordData->getPlainPassword();
        $result = $this->userPasswordChanger->completeUserPasswordChange($userPasswordChange, $plainPassword);

        $event = new UserPasswordChangeCompletedEvent($passwordData, $userPasswordChange, $result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}