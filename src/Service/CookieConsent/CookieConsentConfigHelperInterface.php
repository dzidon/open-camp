<?php

namespace App\Service\CookieConsent;

/**
 * Helps with retrieving info regarding cookie consent configuration.
 */
interface CookieConsentConfigHelperInterface
{
    /**
     * Returns true if users are required to give consent to cookie usage.
     *
     * @return bool
     */
    public function appAsksForCookieConsent(): bool;

    /**
     * Returns true if the given cookie consent is enabled.
     *
     * @param string $cookieConsent
     * @return bool
     */
    public function isCookieConsentEnabled(string $cookieConsent): bool;
}