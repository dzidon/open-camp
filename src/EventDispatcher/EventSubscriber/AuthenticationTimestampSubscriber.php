<?php

namespace App\EventDispatcher\EventSubscriber;

use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Sets the "last active at" timestamp when the user logs in.
 */
class AuthenticationTimestampSubscriber
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLogin(LoginSuccessEvent $event): void
    {
        $token = $event->getAuthenticatedToken();
        $user = $token->getUser();

        if (!$user instanceof User)
        {
            return;
        }

        $user->setLastActiveAt(new DateTimeImmutable('now'));
        $this->userRepository->saveUser($user, true);
    }
}