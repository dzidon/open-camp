<?php

namespace App\Service\CookieConsent;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class CookieConsentConfigHelper implements CookieConsentConfigHelperInterface
{
    private string $googleTagManagerId;

    private array $enabledCookieConsents;

    public function __construct(
        #[Autowire(env: 'GOOGLE_TAG_MANAGER_ID')]
        string $googleTagManagerId,

        #[Autowire('%app.enabled_cookie_consents%')]
        array $enabledCookieConsents
    ) {
        $this->googleTagManagerId = $googleTagManagerId;
        $this->enabledCookieConsents = $enabledCookieConsents;
    }

    /**
     * @inheritDoc
     */
    public function appAsksForCookieConsent(): bool
    {
        $atLeastOneCookieConsentEnabled = false;

        foreach ($this->enabledCookieConsents as $enabled)
        {
            if ($enabled)
            {
                $atLeastOneCookieConsentEnabled = true;

                break;
            }
        }

        return !empty($this->googleTagManagerId) && $atLeastOneCookieConsentEnabled;
    }

    /**
     * @inheritDoc
     */
    public function isCookieConsentEnabled(string $cookieConsent): bool
    {
        return array_key_exists($cookieConsent, $this->enabledCookieConsents) && $this->enabledCookieConsents[$cookieConsent];
    }
}