<?php

namespace App\Service\Security\Authentication;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Creates redirects to social login.
 */
interface SocialLoginRedirectResponseFactoryInterface
{
    /**
     * Creates a redirect response that leads to third-party provider's login page.
     *
     * @param string $service
     * @return RedirectResponse
     */
    public function createRedirectResponse(string $service): RedirectResponse;
}