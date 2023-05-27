<?php

namespace App\Tests\Translation;

use App\Translation\LocaleRedirectResponseFactory;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the class that redirects users to translated routes.
 */
class LocaleRedirectResponseFactoryTest extends KernelTestCase
{
    /**
     * If the locale cookie is not set, user is redirected to the default locale.
     *
     * @return void
     * @throws Exception
     */
    public function testNullCookieLocale(): void
    {
        $factory = $this->getLocaleRedirectResponseFactory();
        $request = $this->createRequest(null);
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/en/route/mock', $response->getTargetUrl());
    }

    /**
     * If the locale cookie contains a supported locale, user is redirected to it.
     *
     * @return void
     * @throws Exception
     */
    public function testValidCookieLocale(): void
    {
        $factory = $this->getLocaleRedirectResponseFactory();
        $request = $this->createRequest('cs');
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/cs/route/mock', $response->getTargetUrl());
    }

    /**
     * If the locale cookie contains an unsupported locale, user is redirected to the default locale.
     *
     * @return void
     * @throws Exception
     */
    public function testInvalidCookieLocale(): void
    {
        $factory = $this->getLocaleRedirectResponseFactory();
        $request = $this->createRequest('fr');
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/en/route/mock', $response->getTargetUrl());
    }

    /**
     * Tests that current GET parameters are inserted into the newly created URL.
     *
     * @return void
     * @throws Exception
     */
    public function testGetParameters(): void
    {
        $factory = $this->getLocaleRedirectResponseFactory();
        $request = $this->createRequest(null);
        $request->query->set('abc', 123);
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/en/route/mock?abc=123', $response->getTargetUrl());
    }

    /**
     * Creates a request object with the specified locale cookie.
     *
     * @param string|null $cookieLocale
     * @return Request
     */
    private function createRequest(string|null $cookieLocale): Request
    {
        $request = new Request();
        if ($cookieLocale !== null)
        {
            $request->cookies->set('locale', $cookieLocale);
        }

        return $request;
    }

    /**
     * Gets an instance of LocaleRedirectResponseFactory from the service container.
     *
     * @return LocaleRedirectResponseFactory
     * @throws Exception
     */
    private function getLocaleRedirectResponseFactory(): LocaleRedirectResponseFactory
    {
        $container = static::getContainer();

        /** @var LocaleRedirectResponseFactory $factory */
        $factory = $container->get(LocaleRedirectResponseFactory::class);

        return $factory;
    }
}