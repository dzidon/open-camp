<?php

namespace App\Tests\Security;

use App\Security\AuthenticationEntryPoint;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

/**
 * Tests the main authentication entry point.
 */
class AuthenticationEntryPointTest extends KernelTestCase
{
    /**
     * Tests that the user gets informed about having to repeat login before accessing a protected resource.
     *
     * @return void
     * @throws Exception
     */
    public function testAuthenticatedRemembered(): void
    {
        $request = $this->createRequest();
        $entryPoint = $this->getAuthenticationEntryPoint(['IS_AUTHENTICATED_REMEMBERED']);
        $entryPoint->start($request);

        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $this->assertTrue($flashBag->has('warning'));
        $messages = $flashBag->peek('warning');
        $this->assertTrue(in_array('auth.login_repeat_required', $messages));
    }

    /**
     * Tests that the user gets informed about having to log in before accessing a protected resource.
     *
     * @return void
     * @throws Exception
     */
    public function testNotAuthenticated(): void
    {
        $request = $this->createRequest();
        $entryPoint = $this->getAuthenticationEntryPoint();
        $entryPoint->start($request);

        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $this->assertTrue($flashBag->has('warning'));
        $messages = $flashBag->peek('warning');
        $this->assertTrue(in_array('auth.login_required', $messages));
    }

    /**
     * Creates a request with a mocked session and a working flash bag.
     *
     * @return Request
     */
    private function createRequest(): Request
    {
        $request = new Request();
        $sessionMock = $this->createSessionMock();

        $request->setSession($sessionMock);

        return $request;
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

    /**
     * Returns an instance of the authentication entry point from the service container.
     *
     * @param array $userRoles
     * @return AuthenticationEntryPoint
     * @throws Exception
     */
    private function getAuthenticationEntryPoint(array $userRoles = []): AuthenticationEntryPoint
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var Security|MockObject $securityMock */
        $securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $securityMock
            ->expects($this->any())
            ->method('isGranted')
            ->willReturnCallback(function (string $role) use ($userRoles)
            {
                return in_array($role, $userRoles);
            })
        ;

        $container->set(Security::class, $securityMock);

        /** @var AuthenticationEntryPoint $entryPoint */
        $entryPoint = $container->get(AuthenticationEntryPoint::class);

        return $entryPoint;
    }
}