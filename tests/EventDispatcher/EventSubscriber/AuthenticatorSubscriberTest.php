<?php

namespace App\Tests\EventDispatcher\EventSubscriber;

use App\EventDispatcher\EventSubscriber\AuthenticationSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Tests that a flash message gets added to the flash bag after logging out.
 */
class AuthenticatorSubscriberTest extends KernelTestCase
{
    private AuthenticationSubscriber $authSubscriber;

    /**
     * Tests that a message is added to the flash bag if a user logs in.
     *
     * @return void
     */
    public function testOnLogin(): void
    {
        $event = $this->createLoginSuccessEvent(false);
        $this->authSubscriber->onLogin($event);

        $flashBag = $this->eventGetFlashBag($event);
        $this->assertTrue($flashBag->has('success'));

        $messages = $flashBag->peek('success');
        $this->assertTrue(in_array('auth.logged_in', $messages));
    }

    /**
     * Tests that no message is added when a user is authenticated using remember me cookie.
     *
     * @return void
     */
    public function testOnLoginRememberMe(): void
    {
        $event = $this->createLoginSuccessEvent(true);
        $this->authSubscriber->onLogin($event);

        $flashBag = $this->eventGetFlashBag($event);
        $this->assertFalse($flashBag->has('success'));
    }

    /**
     * Tests that no message is added to the flash bag if no user was logged in.
     *
     * @return void
     */
    public function testOnLogoutWithoutToken(): void
    {
        $event = $this->createLogoutEvent(false);
        $this->authSubscriber->onLogout($event);

        $flashBag = $this->eventGetFlashBag($event);
        $this->assertFalse($flashBag->has('success'));
    }

    /**
     * Tests that a message is added to the flash bag if there was a user logged in.
     *
     * @return void
     */
    public function testOnLogoutWithToken(): void
    {
        $event = $this->createLogoutEvent(true);
        $this->authSubscriber->onLogout($event);

        $flashBag = $this->eventGetFlashBag($event);
        $this->assertTrue($flashBag->has('success'));

        $messages = $flashBag->peek('success');
        $this->assertTrue(in_array('auth.logged_out', $messages));
    }

    /**
     * Returns a flash bag from an event.
     *
     * @param LoginSuccessEvent|LogoutEvent $event
     * @return FlashBagInterface
     */
    private function eventGetFlashBag(LoginSuccessEvent|LogoutEvent $event): FlashBagInterface
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        return $session->getFlashBag();
    }

    /**
     * Creates a logout event.
     *
     * @param bool $withToken Was there a user logged in before logging out?
     * @return LogoutEvent
     */
    private function createLogoutEvent(bool $withToken): LogoutEvent
    {
        $tokenMock = null;

        if ($withToken)
        {
            /** @var TokenInterface|MockObject $translatorMock */
            $tokenMock = $this->getMockBuilder(TokenInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
        }

        $request = new Request();
        $sessionMock = $this->createSessionMock();

        $request->setSession($sessionMock);

        return new LogoutEvent($request, $tokenMock);
    }

    /**
     * Creates a successful login event.
     *
     * @param bool $rememberMeToken
     * @return LoginSuccessEvent
     */
    private function createLoginSuccessEvent(bool $rememberMeToken): LoginSuccessEvent
    {
        $request = new Request();
        $sessionMock = $this->createSessionMock();

        $request->setSession($sessionMock);

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

        $tokenClass = TokenInterface::class;
        if ($rememberMeToken)
        {
            $tokenClass = RememberMeToken::class;
        }

        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->getMockBuilder($tokenClass)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return new LoginSuccessEvent($authenticatorMock, $passportMock, $tokenMock, $request, null, '');
    }

    /**
     * Mocks the FlashBagAwareSessionInterface.
     *
     * @return FlashBagAwareSessionInterface
     */
    private function createSessionMock(): FlashBagAwareSessionInterface
    {
        /** @var FlashBagAwareSessionInterface|MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder(FlashBagAwareSessionInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $flashBag = new FlashBag();

        $sessionMock
            ->expects($this->any())
            ->method('getFlashBag')
            ->willReturnCallback(function () use ($flashBag)
            {
                return $flashBag;
            })
        ;

        return $sessionMock;
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var AuthenticationSubscriber $authSubscriber */
        $authSubscriber = $container->get(AuthenticationSubscriber::class);
        $this->authSubscriber = $authSubscriber;
    }
}