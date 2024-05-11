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
    private array $scopes;

    private ClientRegistry $clientRegistry;

    public function __construct(
        ClientRegistry $clientRegistry,

        #[Autowire('%app.social_login_scopes%')]
        array $scopes
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->scopes = $scopes;
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse(string $service): RedirectResponse
    {
        $scopesForService = [];

        if (array_key_exists($service, $this->scopes))
        {
            $scopesForService = $this->scopes[$service];
        }

        $client = $this->clientRegistry->getClient($service);

        return $client->redirect($scopesForService, []);
    }
}