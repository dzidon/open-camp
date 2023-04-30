<?php

namespace App\Tests\Functional\Translation;

use App\Translation\LocaleGuesser;
use App\Translation\LocaleRedirectResponseFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Tests the class that redirects users to translated routes.
 */
class LocaleRedirectResponseFactoryTest extends KernelTestCase
{
    /**
     * If the locale cookie is not set, user is redirected to the default locale.
     *
     * @return void
     */
    public function testNullCookieLocale(): void
    {
        $factory = $this->createLocaleRedirectResponseFactory();
        $request = $this->createRequest(null);
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/en/route/mock', $response->getTargetUrl());
    }

    /**
     * If the locale cookie contains a supported locale, user is redirected to it.
     *
     * @return void
     */
    public function testValidCookieLocale(): void
    {
        $factory = $this->createLocaleRedirectResponseFactory();
        $request = $this->createRequest('cs');
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/cs/route/mock', $response->getTargetUrl());
    }

    /**
     * If the locale cookie contains an unsupported locale, user is redirected to the default locale.
     *
     * @return void
     */
    public function testInvalidCookieLocale(): void
    {
        $factory = $this->createLocaleRedirectResponseFactory();
        $request = $this->createRequest('fr');
        $response = $factory->createRedirectResponse($request, 'locale_route_mock');
        $this->assertSame('/en/route/mock', $response->getTargetUrl());
    }

    /**
     * Tests that current GET parameters are inserted into the newly created URL.
     *
     * @return void
     */
    public function testGetParameters(): void
    {
        $factory = $this->createLocaleRedirectResponseFactory();
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
     * Instantiates LocaleRedirectResponseFactory.
     *
     * @return LocaleRedirectResponseFactory
     */
    private function createLocaleRedirectResponseFactory(): LocaleRedirectResponseFactory
    {
        self::bootKernel();
        $container = static::getContainer();

        $locales = ['en', 'cs', 'de'];
        $defaultLocale = 'en';
        $localeGuesser = new LocaleGuesser($locales, $defaultLocale);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        return new LocaleRedirectResponseFactory($localeGuesser, $urlGenerator, $locales);
    }
}