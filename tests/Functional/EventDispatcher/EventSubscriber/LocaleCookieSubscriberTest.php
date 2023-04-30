<?php

namespace App\Tests\Functional\EventDispatcher\EventSubscriber;

use App\EventDispatcher\EventSubscriber\LocaleCookieSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Tests the subscriber that stores user's last locale in a cookie.
 */
class LocaleCookieSubscriberTest extends TestCase
{
    /**
     * If there are no supported locales, the response won't be affected.
     *
     * @return void
     */
    public function testNoSupportedLocales(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber([]);
        $event = $this->createResponseEvent('route_mock', 'en');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNull($cookie);
    }

    /**
     * If there is only one supported locale, the response won't be affected.
     *
     * @return void
     */
    public function testOneSupportedLocale(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en']);
        $event = $this->createResponseEvent('route_mock', 'en');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNull($cookie);
    }

    /**
     * If the request is a XMLHttpRequest, the response won't be affected.
     *
     * @return void
     */
    public function testXmlHttpRequest(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('route_mock', 'en');
        $request = $event->getRequest();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNull($cookie);
    }

    /**
     * If the route is 'user_home_no_locale', the response won't be affected.
     *
     * @return void
     */
    public function testNoLocaleRoute(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('user_home_no_locale', 'en');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNull($cookie);
    }

    /**
     * If the locale cookie is not set, it gets added to the response.
     *
     * @return void
     */
    public function testCookieNotSet(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('locale_route_mock', 'cs');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNotNull($cookie);
        $this->assertSame('cs', $cookie->getValue());
    }

    /**
     * If the cookie and request locales are not equal, the request locale is added to the response.
     *
     * @return void
     */
    public function testCookieValid(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('locale_route_mock', 'cs', 'en');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNotNull($cookie);
        $this->assertSame('cs', $cookie->getValue());
    }

    /**
     * If the locale cookie has an invalid value (unsupported locale), it gets rewritten by the request locale.
     *
     * @return void
     */
    public function testCookieInvalid(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('locale_route_mock', 'cs', 'aaa');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNotNull($cookie);
        $this->assertSame('cs', $cookie->getValue());
    }

    /**
     * If the cookie and request locales are equal, the response is not affected.
     *
     * @return void
     */
    public function testCookieUnchanged(): void
    {
        $subscriber = $this->createLocaleCookieSubscriber(['en', 'cs']);
        $event = $this->createResponseEvent('locale_route_mock', 'cs', 'cs');

        $subscriber->onKernelResponse($event);
        $response = $event->getResponse();
        $cookie = $this->responseGetLocaleCookie($response);

        $this->assertNull($cookie);
    }

    /**
     * Returns the locale cookie from a response or null if it's not found.
     *
     * @param Response $response
     * @return Cookie|null
     */
    private function responseGetLocaleCookie(Response $response): ?Cookie
    {
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie)
        {
            if ($cookie->getName() === 'locale')
            {
                return $cookie;
            }
        }

        return null;
    }

    /**
     * Instantiates a response event which holds a request and a response.
     *
     * @param string $requestRoute
     * @param string $requestLocale
     * @param string|null $cookieLocale
     * @return ResponseEvent
     */
    private function createResponseEvent(string $requestRoute,
                                         string $requestLocale,
                                         string|null $cookieLocale = null): ResponseEvent
    {
        /** @var HttpKernelInterface|MockObject $httpKernel */
        $httpKernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $request = new Request();
        $request->setLocale($requestLocale);

        $attributes = $request->attributes;
        $attributes->set('_route', $requestRoute);
        $attributes->set('_locale', $requestLocale);
        $attributes->set('_route_params', [
            '_locale' => $requestLocale
        ]);

        if ($cookieLocale !== null)
        {
            $request->cookies->set('locale', $cookieLocale);
        }

        $response = new Response();

        return new ResponseEvent($httpKernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);
    }

    /**
     * Instantiates the locale cookie subscriber.
     *
     * @param array $locales
     * @return LocaleCookieSubscriber
     */
    private function createLocaleCookieSubscriber(array $locales): LocaleCookieSubscriber
    {
        return new LocaleCookieSubscriber($locales, '1 year');
    }
}