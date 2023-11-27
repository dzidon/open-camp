<?php

namespace App\Model\EventSubscriber\User\UserRegistration;

use App\Model\Event\User\UserRegistration\UserRegistrationCreatedEvent;
use App\Model\Event\User\UserRegistration\UserRegistrationCreateEvent;
use App\Model\Service\UserRegistration\UserRegistrationFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserRegistrationCreateSubscriber
{
    private UserRegistrationFactoryInterface $userRegistrationFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UserRegistrationFactoryInterface $userRegistrationFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->userRegistrationFactory = $userRegistrationFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: UserRegistrationCreateEvent::NAME)]
    public function onCreateDispatchResult(UserRegistrationCreateEvent $event): void
    {
        $registrationData = $event->getRegistrationData();
        $email = $registrationData->getEmail();
        $result = $this->userRegistrationFactory->createUserRegistration($email);

        $event = new UserRegistrationCreatedEvent($registrationData, $result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}