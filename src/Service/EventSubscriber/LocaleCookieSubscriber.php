<?php

namespace App\Service\EventSubscriber;

use DateTimeImmutable;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Stores user's current locale in a cookie.
 */
class LocaleCookieSubscriber
{
    private array $locales;
    private string $cookieLifespan;

    public function __construct(array|string $locales, string $cookieLifespan)
    {
        if (is_string($locales))
        {
            $locales = explode('|', $locales);
        }

        $this->locales = $locales;
        $this->cookieLifespan = $cookieLifespan;
    }

    /**
     * Called when a controller action returns a response.
     *
     * @param ResponseEvent $event
     * @return void
     */
    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (count($this->locales) <= 1)
        {
            return;
        }

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest())
        {
            return;
        }

        $route = $request->attributes->get('_route', 'user_home_no_locale');
        if ($route === 'user_home_no_locale')
        {
            return;
        }

        $response = $event->getResponse();
        $requestLocale = $request->getLocale();
        $cookieLocale = $request->cookies->get('locale');

        if ($cookieLocale === null || !in_array($cookieLocale, $this->locales) || $cookieLocale !== $requestLocale)
        {
            $newCookie = $this->createLocaleCookie($requestLocale);
            $response->headers->setCookie($newCookie);
        }
    }

    /**
     * Instantiates and returns a cookie with a specified locale.
     *
     * @param string $locale
     * @return Cookie
     */
    private function createLocaleCookie(string $locale): Cookie
    {
        $offset = sprintf('+%s', $this->cookieLifespan);
        return new Cookie('locale', $locale, new DateTimeImmutable($offset));
    }
}