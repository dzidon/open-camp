<?php

namespace App\Service\Twig;

use App\Service\CookieConsent\CookieConsentConfigHelperInterface;
use App\Service\CookieConsent\CookieConsentHttpStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds cookie consent functions to Twig.
 */
class CookieConsentExtension extends AbstractExtension
{
    private CookieConsentConfigHelperInterface $cookieConsentConfigHelper;

    private CookieConsentHttpStorageInterface $cookieConsentHttpStorage;

    public function __construct(CookieConsentConfigHelperInterface $cookieConsentConfigHelper,
                                CookieConsentHttpStorageInterface  $cookieConsentHttpStorage)
    {
        $this->cookieConsentConfigHelper = $cookieConsentConfigHelper;
        $this->cookieConsentHttpStorage = $cookieConsentHttpStorage;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_asks_for_cookie_consent', [$this->cookieConsentConfigHelper, 'appAsksForCookieConsent']),
            new TwigFunction('is_cookie_consent_granted', [$this->cookieConsentHttpStorage, 'isCookieConsentGranted']),
            new TwigFunction('is_cookie_consents_decision_available', [$this, 'isCookieConsentsDecisionAvailable']),
        ];
    }

    /**
     * True means that we know what cookie consents are granted by the user.
     *
     * @return bool
     */
    public function isCookieConsentsDecisionAvailable(): bool
    {
        return $this->cookieConsentHttpStorage->getGrantedCookieConsents() !== null;
    }
}