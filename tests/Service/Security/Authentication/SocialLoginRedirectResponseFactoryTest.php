<?php

namespace App\Tests\Service\Security\Authentication;

use App\Service\Security\Authentication\SocialLoginRedirectResponseFactory;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tests social login redirects.
 */
class SocialLoginRedirectResponseFactoryTest extends KernelTestCase
{
    /**
     * Tests that scopes of a valid service are passed into 'redirect'.
     *
     * @return void
     */
    public function testValidService(): void
    {
        $factory = $this->createSocialLoginRedirectResponseFactory();
        $redirectResponse = $factory->createRedirectResponse('facebook');
        $url = $redirectResponse->getTargetUrl();

        $this->assertSame('site.com?scopes=public_profile,email', $url);
    }

    /**
     * Tests that scopes of an invalid service are not passed into 'redirect'.
     *
     * @return void
     */
    public function testInvalidService(): void
    {
        $factory = $this->createSocialLoginRedirectResponseFactory();
        $redirectResponse = $factory->createRedirectResponse('invalid');
        $url = $redirectResponse->getTargetUrl();

        $this->assertSame('site.com', $url);
    }

    /**
     * Returns an instance of SocialLoginRedirectResponseFactory.
     *
     * @return SocialLoginRedirectResponseFactory
     */
    private function createSocialLoginRedirectResponseFactory(): SocialLoginRedirectResponseFactory
    {
        $container = static::getContainer();

        /** @var OAuth2ClientInterface|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(OAuth2ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $clientMock
            ->expects($this->any())
            ->method('redirect')
            ->willReturnCallback(function (array $scopes)
            {
                $url = 'site.com';

                if (!empty($scopes))
                {
                    $url .= '?scopes=' . implode(',', $scopes);
                }

                return new RedirectResponse($url);
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

        $socialLoginServicesData = $container->getParameter('app.social_login_services');

        return new SocialLoginRedirectResponseFactory($clientRegistryMock, $socialLoginServicesData);
    }
}