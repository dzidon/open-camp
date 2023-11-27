<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreatedEvent;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use App\Model\Service\UserPasswordChange\UserPasswordChangeFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserPasswordChangeCreateSubscriber
{
    private UserPasswordChangeFactoryInterface $passwordChangeFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UserPasswordChangeFactoryInterface $passwordChangeFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->passwordChangeFactory = $passwordChangeFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: UserPasswordChangeCreateEvent::NAME)]
    public function onCreateDispatchResult(UserPasswordChangeCreateEvent $event): void
    {
        $passwordChangeData = $event->getPasswordChangeData();
        $email = $passwordChangeData->getEmail();
        $result = $this->passwordChangeFactory->createUserPasswordChange($email);

        $event = new UserPasswordChangeCreatedEvent($passwordChangeData, $result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }
}