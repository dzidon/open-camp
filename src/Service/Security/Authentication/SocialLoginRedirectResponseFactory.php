<?php

namespace App\Service\Security\Authentication;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @inheritDoc
 */
class SocialLoginRedirectResponseFactory implements SocialLoginRedirectResponseFactoryInterface
{
    private ClientRegistry $clientRegistry;

    private array $socialLoginServicesData;

    public function __construct(
        ClientRegistry $clientRegistry,

        #[Autowire('%app.social_login_services%')]
        array $socialLoginServicesData
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->socialLoginServicesData = $socialLoginServicesData;
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse(string $service): RedirectResponse
    {
        $scopesForService = [];

        if (array_key_exists($service, $this->socialLoginServicesData) &&
            array_key_exists('scopes', $this->socialLoginServicesData[$service]))
        {
            $scopesForService = $this->socialLoginServicesData[$service]['scopes'];
        }

        $client = $this->clientRegistry->getClient($service);

        return $client->redirect($scopesForService, []);
    }
}