<?php

namespace App\Tests\Service\Security\Authentication;

use App\Library\Security\Authentication\Exception\AlreadyAuthenticatedException;
use App\Library\Security\Authentication\Exception\SocialUserNotFoundException;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Security\Authentication\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Tests the social authenticator (Social login using Facebook, Google, etc.).
 */
class SocialAuthenticatorTest extends KernelTestCase
{
    /**
     * Tests that the authenticator can only be used on the route 'user_login_oauth_check'.
     *
     * @return void
     */
    public function testSupportsWithUnsupportedRoute(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('facebook', 'unsupported_route');

        $this->assertFalse($socialAuthenticator->supports($request));
    }

    /**
     * Tests that the authenticator can only be used on enabled services.
     *
     * @return void
     */
    public function testSupportsWithUnsupportedService(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('unsupported_service', 'user_login_oauth_check');

        $this->assertFalse($socialAuthenticator->supports($request));
    }

    /**
     * Tests that the authenticator can be used with an enabled service and on the route "user_login_oauth_check".
     *
     * @return void
     */
    public function testSupportsWithSupportedParams(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('facebook', 'user_login_oauth_check');

        $this->assertTrue($socialAuthenticator->supports($request));
    }

    /**
     * Tests that the user is redirected home if there is no target path specified in the session.
     *
     * @return void
     */
    public function testOnAuthenticationSuccessNoTargetPath(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $token = $this->createTokenMock();

        $redirectResponse = $socialAuthenticator->onAuthenticationSuccess($request, $token, 'main');
        $this->assertSame('/', $redirectResponse->getTargetUrl());
    }

    /**
     * Tests that if there is a target path specified in the session, the user is redirected to it.
     *
     * @return void
     */
    public function testOnAuthenticationSuccessWithTargetPath(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('facebook', 'user_login_oauth_check', 'target/path');
        $token = $this->createTokenMock();

        $redirectResponse = $socialAuthenticator->onAuthenticationSuccess($request, $token, 'main');
        $this->assertSame('target/path', $redirectResponse->getTargetUrl());
    }

    /**
     * Tests that on failure, a message is added to the flash bag and the user is redirected home.
     *
     * @return void
     */
    public function testOnAuthenticationFailure(): void
    {
        $socialAuthenticator = $this->getSocialAuthenticator();
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $exception = new AuthenticationException();

        $redirectResponse = $socialAuthenticator->onAuthenticationFailure($request, $exception);
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $this->assertTrue($flashBag->has('failure'));
        $messages = $flashBag->peek('failure');
        $this->assertTrue(in_array('auth.social_login_failed', $messages));

        $this->assertSame('/', $redirectResponse->getTargetUrl());
    }

    /**
     * When the user is fully authenticated, the authentication fails.
     *
     * @return void
     */
    public function testAuthenticateWhenAuthenticatedFully(): void
    {
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $socialAuthenticator = $this->getSocialAuthenticator(['IS_AUTHENTICATED_FULLY'], null);

        $this->expectException(AlreadyAuthenticatedException::class);
        $socialAuthenticator->authenticate($request);
    }

    /**
     * If a third-party service does not return a user, the authentication fails.
     *
     * @return void
     */
    public function testAuthenticateEmailNotFound(): void
    {
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $socialAuthenticator = $this->getSocialAuthenticator([], null);

        $this->expectException(SocialUserNotFoundException::class);
        $socialAuthenticator->authenticate($request);
    }

    /**
     * Tests authenticating an existing user.
     *
     * @return void
     */
    public function testAuthenticateExistingUser(): void
    {
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $socialAuthenticator = $this->getSocialAuthenticator([], 'david@gmail.com');

        $passport = $socialAuthenticator->authenticate($request);

        /** @var User $user */
        $user = $passport->getUser();
        $this->assertSame('david@gmail.com', $user->getEmail());
    }

    /**
     * Tests authenticating a new user. A new record is created in the DB.
     *
     * @return void
     */
    public function testAuthenticateNewUser(): void
    {
        $request = $this->createRequest('facebook', 'user_login_oauth_check');
        $socialAuthenticator = $this->getSocialAuthenticator([], 'new_social_user@yahoo.com');
        $userRepository = $this->getUserRepository();

        $this->assertNull($userRepository->findOneByEmail('new_social_user@yahoo.com'));
        $passport = $socialAuthenticator->authenticate($request);

        /** @var User $user */
        $user = $passport->getUser();
        $this->assertSame('new_social_user@yahoo.com', $user->getEmail());

        $newUser = $userRepository->findOneByEmail('new_social_user@yahoo.com');
        $this->assertNotNull($newUser);
        $this->assertSame('new_social_user@yahoo.com', $newUser->getEmail());
    }

    /**
     * Creates a request.
     *
     * @param string|null $service
     * @param string $route
     * @param string|null $targetPath
     * @return Request
     */
    private function createRequest(?string $service, string $route, ?string $targetPath = null): Request
    {
        $request = new Request([], [], ['_route' => $route]);
        if ($service !== null)
        {
            $request->attributes->set('service', $service);
        }

        $session = $this->createSessionMock($targetPath);
        $request->setSession($session);

        return $request;
    }

    /**
     * Mocks the FlashBagAwareSessionInterface.
     *
     * @param string|null $targetPath
     * @return FlashBagAwareSessionInterface
     */
    private function createSessionMock(?string $targetPath): FlashBagAwareSessionInterface
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

        $sessionMock
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback(function (string $key) use ($targetPath)
            {
                if ($key === '_security.main.target_path')
                {
                    return $targetPath;
                }

                return null;
            })
        ;

        return $sessionMock;
    }

    /**
     * Mocks the TokenInterface.
     *
     * @return TokenInterface
     */
    private function createTokenMock(): TokenInterface
    {
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $tokenMock;
    }

    /**
     * Mocks the ClientRegistry.
     *
     * @param string|null $socialEmail
     * @return ClientRegistry
     */
    private function createClientRegistryMock(?string $socialEmail): ClientRegistry
    {
        /** @var AccessToken|MockObject $accessTokenMock */
        $accessTokenMock = $this->getMockBuilder(AccessToken::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $accessTokenMock
            ->expects($this->any())
            ->method('getToken')
            ->willReturnCallback(function ()
            {
                return '123';
            })
        ;

        /** @var FacebookUser|MockObject $socialUserMock */
        $socialUserMock = $this->getMockBuilder(FacebookUser::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $socialUserMock
            ->expects($this->any())
            ->method('getEmail')
            ->willReturnCallback(function () use ($socialEmail)
            {
                return $socialEmail;
            })
        ;

        /** @var OAuth2ClientInterface|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(OAuth2ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $clientMock
            ->expects($this->any())
            ->method('getAccessToken')
            ->willReturnCallback(function () use ($accessTokenMock)
            {
                return $accessTokenMock;
            })
        ;

        $clientMock
            ->expects($this->any())
            ->method('fetchUserFromToken')
            ->willReturnCallback(function () use ($socialUserMock)
            {
                return $socialUserMock;
            })
        ;

        /** @var ClientRegistry|MockObject $clientRegistryMock */
        $clientRegistryMock = $this->getMockBuilder(ClientRegistry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $clientRegistryMock
            ->expects($this->any())
            ->method('getClient')
            ->willReturnCallback(function () use ($clientMock)
            {
                return $clientMock;
            })
        ;

        return $clientRegistryMock;
    }

    /**
     * Mocks the Security helper.
     *
     * @param array $userRoles
     * @return Security
     */
    private function createSecurityMock(array $userRoles): Security
    {
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

        return $securityMock;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $container->get(UserRepositoryInterface::class);

        return $userRepository;
    }

    private function getSocialAuthenticator(array $userRoles = [], ?string $socialEmail = ''): SocialAuthenticator
    {
        $container = static::getContainer();

        $clientRegistry = $this->createClientRegistryMock($socialEmail);
        $security = $this->createSecurityMock($userRoles);
        $userRepository = $this->getUserRepository();

        /** @var TranslatorInterface $translator */
        $translator = $container->get(TranslatorInterface::class);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        return new SocialAuthenticator($clientRegistry, $userRepository, $translator, $urlGenerator, $security, ['facebook']);
    }
}