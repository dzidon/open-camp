<?php

namespace App\Service\EventSubscriber;

use App\Service\CookieConsent\CookieConsentConfigHelperInterface;
use App\Service\CookieConsent\CookieConsentHttpStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Handles user's cookie consents.
 */
class CookieConsentHttpStorageSubscriber
{
    private CookieConsentHttpStorageInterface $cookieConsentHttpStorage;

    private CookieConsentConfigHelperInterface $cookieConsentConfigHelper;

    private UrlGeneratorInterface $urlGenerator;

    private string $postParameterCookieConsentGrantAll;

    private string $postParameterCookieConsentDenyAll;

    private string $postParameterCookieConsentGrantPreferences;

    private string $postParameterCookieConsentPreferences;

    public function __construct(
        CookieConsentHttpStorageInterface  $cookieConsentHttpStorage,
        CookieConsentConfigHelperInterface $cookieConsentConfigHelper,
        UrlGeneratorInterface              $urlGenerator,

        #[Autowire('%app.post_param_cookie_consent_grant_all%')]
        string $postParameterCookieConsentGrantAll,

        #[Autowire('%app.post_param_cookie_consent_deny_all%')]
        string $postParameterCookieConsentDenyAll,

        #[Autowire('%app.post_param_cookie_consent_grant_preferences%')]
        string $postParameterCookieConsentGrantPreferences,

        #[Autowire('%app.post_param_cookie_consent_preferences%')]
        string $postParameterCookieConsentPreferences
    ) {
        $this->cookieConsentHttpStorage = $cookieConsentHttpStorage;
        $this->cookieConsentConfigHelper = $cookieConsentConfigHelper;
        $this->urlGenerator = $urlGenerator;
        $this->postParameterCookieConsentGrantAll = $postParameterCookieConsentGrantAll;
        $this->postParameterCookieConsentDenyAll = $postParameterCookieConsentDenyAll;
        $this->postParameterCookieConsentGrantPreferences = $postParameterCookieConsentGrantPreferences;
        $this->postParameterCookieConsentPreferences = $postParameterCookieConsentPreferences;
    }

    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$this->cookieConsentConfigHelper->appAsksForCookieConsent())
        {
            return;
        }

        $request = $event->getRequest();

        if (!$request->isMethod('POST'))
        {
            return;
        }

        $denyAll = $request->request->has($this->postParameterCookieConsentDenyAll);
        $grantAll = $request->request->has($this->postParameterCookieConsentGrantAll);
        $grantPreferences = $request->request->has($this->postParameterCookieConsentGrantPreferences);

        if ($denyAll || $grantAll || $grantPreferences)
        {
            $response = $this->createRedirectResponse($request);

            if ($denyAll)
            {
                $this->cookieConsentHttpStorage->denyAllCookieConsents($response);
            }
            else if ($grantAll)
            {
                $this->cookieConsentHttpStorage->grantAllCookieConsents($response);
            }
            else if ($grantPreferences)
            {
                $chosenCookieConsents = [];
                $cookieConsentPreferences = $request->request->all($this->postParameterCookieConsentPreferences);

                foreach ($cookieConsentPreferences as $cookieConsentPreference => $switchPlaceholder)
                {
                    if ($this->cookieConsentConfigHelper->isCookieConsentEnabled($cookieConsentPreference))
                    {
                        $chosenCookieConsents[] = $cookieConsentPreference;
                    }
                }

                $this->cookieConsentHttpStorage->setGrantedCookieConsents($chosenCookieConsents, $response);
            }

            $event->setResponse($response);
        }
    }

    private function createRedirectResponse(Request $request): RedirectResponse
    {
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();
        $params = array_merge($routeParams, $queryParams);
        $url = $this->urlGenerator->generate($route, $params);

        return new RedirectResponse($url);
    }
}