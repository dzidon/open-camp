<?php

namespace App\Service\CookieConsent;

use Symfony\Component\HttpFoundation\Response;

/**
 * Can be used to get and set user's cookie consents.
 */
interface CookieConsentHttpStorageInterface
{
    /**
     * Returns an array in which keys represent enabled cookie consents and values are booleans (true = granted).
     * Returns null if the user hasn't granted or denied any consents yet.
     * Returns null if new cookie consents have been added to the app.
     *
     * @return null|string[]
     */
    public function getGrantedCookieConsents(): ?array;

    /**
     * Sets the given cookie consents as granted.
     *
     * @param string[] $cookieConsents
     * @param Response $response
     * @return void
     */
    public function setGrantedCookieConsents(array $cookieConsents, Response $response): void;

    /**
     * Sets all enabled cookie consents as granted.
     *
     * @param Response $response
     * @return void
     */
    public function grantAllCookieConsents(Response $response): void;

    /**
     * Sets all enabled cookie consents as denied.
     *
     * @param Response $response
     * @return void
     */
    public function denyAllCookieConsents(Response $response): void;

    /**
     * Returns true if the given cookie consent is granted.
     *
     * @param string $cookieConsent
     * @return bool
     */
    public function isCookieConsentGranted(string $cookieConsent): bool;
}