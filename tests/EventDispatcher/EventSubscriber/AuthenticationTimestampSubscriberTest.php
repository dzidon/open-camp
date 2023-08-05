<?php

namespace App\Tests\EventDispatcher\EventSubscriber;

use App\EventDispatcher\EventSubscriber\AuthenticationTimestampSubscriber;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AuthenticationTimestampSubscriberTest extends KernelTestCase
{
    public function testOnLogin(): void
    {
        $userRepository = $this->getUserRepository();
        $subscriber = $this->getAuthenticationTimestampSubscriber();
        $user = new User('bob@gmail.com');
        $event = $this->createLoginSuccessEvent($user);

        $subscriber->onLogin($event);
        $loadedUser = $userRepository->findOneByEmail('bob@gmail.com');

        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $loadedUser->getLastActiveAt()->getTimestamp());
    }

    private function createLoginSuccessEvent(User $user): LoginSuccessEvent
    {
        $request = new Request();

        /** @var AuthenticatorInterface|MockObject $sessionMock */
        $authenticatorMock = $this->getMockBuilder(AuthenticatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var Passport|MockObject $passportMock */
        $passportMock = $this->getMockBuilder(Passport::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock
            ->expects($this->any())
            ->method('getUser')
            ->willReturnCallback(function () use ($user)
            {
                return $user;
            })
        ;

        return new LoginSuccessEvent($authenticatorMock, $passportMock, $tokenMock, $request, null, '');
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getAuthenticationTimestampSubscriber(): AuthenticationTimestampSubscriber
    {
        $container = static::getContainer();

        /** @var AuthenticationTimestampSubscriber $subscriber */
        $subscriber = $container->get(AuthenticationTimestampSubscriber::class);

        return $subscriber;
    }
}