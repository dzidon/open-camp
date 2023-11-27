<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCompletedEvent;
use App\Model\Event\User\UserRegistration\UserRegistrationCompleteEvent;
use App\Model\Service\UserRegistration\UserRegistererInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserRegistrationCompleteSubscriber
{
    private UserRegistererInterface $userRegisterer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UserRegistererInterface $userRegisterer, EventDispatcherInterface $eventDispatcher)
    {
        $this->userRegisterer = $userRegisterer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: UserRegistrationCompleteEvent::NAME)]
    public function onCompleteUpdate(UserRegistrationCompleteEvent $event): void
    {
        $userRegistration = $event->getUserRegistration();
        $plainPasswordData = $event->getPlainPasswordData();
        $plainPassword = $plainPasswordData->getPlainPassword();
        $result = $this->userRegisterer->completeUserRegistration($userRegistration, $plainPassword);

        $event = new UserRegistrationCompletedEvent($plainPasswordData, $userRegistration, $result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}