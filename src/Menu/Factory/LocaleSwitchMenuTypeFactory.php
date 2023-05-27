<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Creates a menu that lets users change their locale (language).
 */
class LocaleSwitchMenuTypeFactory extends AbstractMenuTypeFactory
{
    private array $locales;
    private array $localeNames;

    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(RequestStack $requestStack,
                                UrlGeneratorInterface $urlGenerator,
                                array|string $locales,
                                array $localeNames)
    {
        if (is_string($locales))
        {
            $locales = explode('|', $locales);
        }

        $this->locales = $locales;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->localeNames = $localeNames;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'locale_switch';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'locale_switch_root');
        $request = $this->requestStack->getCurrentRequest();
        $attributes = $request->attributes;
        $query = $request->query;

        $attributeParams = $attributes->get('_route_params', []);
        $queryParams = $query->all();
        $routeParams = array_merge($queryParams, $attributeParams);

        $route = $attributes->get('_route', 'user_home');
        $currentLocale = $attributes->get('_locale', '');

        foreach ($this->locales as $locale)
        {
            $routeParams['_locale'] = $locale;
            $url = $this->urlGenerator->generate($route, $routeParams);
            $localeName = $this->getLocaleName($locale);

            $button = new MenuType($locale, 'locale_switch_item', $localeName, $url);
            if ($locale === $currentLocale)
            {
                $button->setActive();
            }

            $button->setParent($menu);
        }

        return $menu;
    }

    /**
     * Converts a locale code into a human-readable string.
     *
     * @param string $locale
     * @return string
     */
    private function getLocaleName(string $locale): string
    {
        if (!array_key_exists($locale, $this->localeNames))
        {
            return $locale;
        }

        return $this->localeNames[$locale];
    }
}