<?php

namespace App\Tests\Security\Authentication;

use App\Security\Authentication\SocialLoginRedirectResponseFactory;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tests social login redirects.
 */
class SocialLoginRedirectResponseFactoryTest extends TestCase
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

        return new SocialLoginRedirectResponseFactory($clientRegistryMock, [
            'facebook' => ['public_profile', 'email'],
        ]);
    }
}