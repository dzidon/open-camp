<?php

namespace App\Service\CookieConsent;

use DateTimeImmutable;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @inheritDoc
 */
class CookieConsentCookieStorage implements CookieConsentHttpStorageInterface
{
    private CookieConsentConfigHelperInterface $cookieConsentConfigHelper;

    private RequestStack $requestStack;

    private string $consentsCookieName;

    private string $consentsCookieLifespan;

    private array $enabledCookieConsents;

    public function __construct(
        CookieConsentConfigHelperInterface $cookieConsentConfigHelper,
        RequestStack                       $requestStack,

        #[Autowire('%app.cookie_name_consents%')]
        string $consentsCookieName,

        #[Autowire('%app.cookie_lifespan_consents%')]
        string $consentsCookieLifespan,

        #[Autowire('%app.enabled_cookie_consents%')]
        array $enabledCookieConsents
    ) {
        $this->cookieConsentConfigHelper = $cookieConsentConfigHelper;
        $this->requestStack = $requestStack;
        $this->consentsCookieName = $consentsCookieName;
        $this->consentsCookieLifespan = $consentsCookieLifespan;
        $this->enabledCookieConsents = $enabledCookieConsents;
    }

    /**
     * @inheritDoc
     */
    public function getGrantedCookieConsents(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookieConsentsJson = $request->cookies->get($this->consentsCookieName);

        if (!is_string($cookieConsentsJson))
        {
            return null;
        }

        $cookieConsents = json_decode($cookieConsentsJson);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            return null;
        }

        $cookieConsentsSanitized = [];

        foreach ($cookieConsents as $cookieConsent => $isGranted)
        {
            if ($this->cookieConsentConfigHelper->isCookieConsentEnabled($cookieConsent))
            {
                $cookieConsentsSanitized[$cookieConsent] = $isGranted;
            }
        }

        foreach ($this->enabledCookieConsents as $cookieConsent => $isEnabled)
        {
            if (!array_key_exists($cookieConsent, $cookieConsentsSanitized) && $isEnabled)
            {
                return null;
            }
        }

        return $cookieConsentsSanitized;
    }

    /**
     * @inheritDoc
     */
    public function setGrantedCookieConsents(array $cookieConsents, Response $response): void
    {
        $grantedCookieConsents = [];

        foreach ($this->enabledCookieConsents as $cookieConsent => $isEnabled)
        {
            if ($isEnabled)
            {
                $grantedCookieConsents[$cookieConsent] = false;
            }
        }

        foreach ($cookieConsents as $cookieConsent)
        {
            if (!$this->cookieConsentConfigHelper->isCookieConsentEnabled($cookieConsent))
            {
                throw new LogicException(
                    sprintf('Cookie consent "%s" passed to "%s" is not enabled. Set it to true in "app.enabled_cookie_consents".', $cookieConsent, __METHOD__)
                );
            }

            $grantedCookieConsents[$cookieConsent] = true;
        }

        $this->setCookie($grantedCookieConsents, $response);
    }

    /**
     * @inheritDoc
     */
    public function grantAllCookieConsents(Response $response): void
    {
        $cookieConsents = [];

        foreach ($this->enabledCookieConsents as $cookieConsent => $isEnabled)
        {
            if ($isEnabled)
            {
                $cookieConsents[$cookieConsent] = true;
            }
        }

        $this->setCookie($cookieConsents, $response);
    }

    /**
     * @inheritDoc
     */
    public function denyAllCookieConsents(Response $response): void
    {
        $cookieConsents = [];

        foreach ($this->enabledCookieConsents as $cookieConsent => $isEnabled)
        {
            if ($isEnabled)
            {
                $cookieConsents[$cookieConsent] = false;
            }
        }

        $this->setCookie($cookieConsents, $response);
    }

    /**
     * @inheritDoc
     */
    public function isCookieConsentGranted(string $cookieConsent): bool
    {
        $grantedCookieConsents = $this->getGrantedCookieConsents();

        if ($grantedCookieConsents === null)
        {
            return false;
        }

        return array_key_exists($cookieConsent, $grantedCookieConsents) && $grantedCookieConsents[$cookieConsent];
    }

    private function setCookie(array $grantedCookieConsents, Response $response)
    {
        $grantedCookieConsentsJson = json_encode($grantedCookieConsents);
        $offset = sprintf('+%s', $this->consentsCookieLifespan);
        $expiresAt = new DateTimeImmutable($offset);
        $cookie = new Cookie($this->consentsCookieName, $grantedCookieConsentsJson, $expiresAt);
        $response->headers->setCookie($cookie);
    }
}