<?php

namespace App\Translation;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @inheritDoc
 */
class LocaleRedirectResponseFactory implements LocaleRedirectResponseFactoryInterface
{
    private LocaleGuesserInterface $localeGuesser;
    private UrlGeneratorInterface $urlGenerator;

    private array $locales;

    public function __construct(LocaleGuesserInterface $localeGuesser,
                                UrlGeneratorInterface $urlGenerator,
                                array|string $locales)
    {
        if (is_string($locales))
        {
            $locales = explode('|', $locales);
        }

        $this->locales = $locales;
        $this->localeGuesser = $localeGuesser;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse(Request $request, string $route): RedirectResponse
    {
        $locale = $request->cookies->get('locale');
        if ($locale === null || !in_array($locale, $this->locales))
        {
            $locale = $this->localeGuesser->guessLocale($request);
        }

        $attributeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();
        $routeParams = array_merge($queryParams, $attributeParams);
        $routeParams['_locale'] = $locale;

        $url = $this->urlGenerator->generate($route, $routeParams);
        return new RedirectResponse($url);
    }
}