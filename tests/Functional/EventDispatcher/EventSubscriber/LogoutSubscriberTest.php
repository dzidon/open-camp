<?php

namespace App\Tests\Functional\EventDispatcher\EventSubscriber;

use App\EventDispatcher\EventSubscriber\LogoutSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Tests that a flash message gets added to the flash bag after logging out.
 */
class LogoutSubscriberTest extends TestCase
{
    private LogoutSubscriber $logoutSubscriber;

    /**
     * Tests that no message is added to the flash bag if no user was logged in.
     *
     * @return void
     */
    public function testOnLogoutWithoutToken(): void
    {
        $event = $this->createLogoutEvent(false);
        $this->logoutSubscriber->onLogout($event);

        $flashBag = $this->logoutEventGetFlashBag($event);
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
        $this->logoutSubscriber->onLogout($event);

        $flashBag = $this->logoutEventGetFlashBag($event);
        $this->assertTrue($flashBag->has('success'));

        $messages = $flashBag->peek('success');
        $this->assertTrue(in_array('You have logged out', $messages));
    }

    /**
     * Returns a flash bag from a logout event.
     *
     * @param LogoutEvent $event
     * @return FlashBagInterface
     */
    private function logoutEventGetFlashBag(LogoutEvent $event): FlashBagInterface
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
        $flashBag = new FlashBag();

        /** @var FlashBagAwareSessionInterface|MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder(FlashBagAwareSessionInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sessionMock
            ->expects($this->any())
            ->method('getFlashBag')
            ->willReturnCallback(function () use ($flashBag)
            {
                return $flashBag;
            })
        ;

        $request->setSession($sessionMock);

        return new LogoutEvent($request, $tokenMock);
    }

    protected function setUp(): void
    {
        /** @var TranslatorInterface|MockObject $translatorMock */
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function (string $id)
            {
                $catalog = [
                    'auth.logged_out' => 'You have logged out',
                ];

                return $catalog[$id];
            })
        ;

        $this->logoutSubscriber = new LogoutSubscriber($translatorMock);
    }
}