<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sets and gets the current route name. Route names are stored in services.yaml under the "app.route_trans_keys"
 * parameter. The current route name is displayed in the title, breadcrumbs, and in the h1 heading.
 */
class RouteNamer implements RouteNamerInterface
{
    private string|null $currentRouteName = null;
    private array $routeTransKeys;

    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator, array $routeTransKeys)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->routeTransKeys = $routeTransKeys;
    }

    /**
     * @inheritDoc
     */
    public function isCurrentRouteNameSet(): bool
    {
        return $this->currentRouteName !== null && $this->currentRouteName !== '';
    }

    /**
     * @inheritDoc
     */
    public function getCurrentRouteName(): ?string
    {
        return $this->currentRouteName;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentTitle(): string
    {
        $fullPageName = $this->translator->trans('app.site_name');
        if ($this->currentRouteName !== null)
        {
            $fullPageName = $this->currentRouteName . ' - ' . $fullPageName;
        }

        return $fullPageName;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentRouteNameByRequest(string $variation = null): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');

        if (is_string($route))
        {
            $this->setCurrentRouteNameByRoute($route, $variation);
        }
    }

    /**
     * @inheritDoc
     */
    public function setCurrentRouteNameByRoute(string $route, string $variation = null): void
    {
        if (array_key_exists($route, $this->routeTransKeys))
        {
            // has multiple variations
            if (is_array($this->routeTransKeys[$route]))
            {
                // no variation specified, use the first one
                if ($variation === null || !array_key_exists($variation, $this->routeTransKeys[$route]))
                {
                    $variation = array_key_first($this->routeTransKeys[$route]);
                }

                $this->currentRouteName = $this->translator->trans($this->routeTransKeys[$route][$variation]);
            }
            else
            {
                $this->currentRouteName = $this->translator->trans($this->routeTransKeys[$route]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function setCurrentRouteName(?string $name): void
    {
        $this->currentRouteName = $name;
    }

    /**
     * @inheritDoc
     */
    public function appendToCurrentRouteName(string $string, bool $addSpace = true): void
    {
        if ($string === '')
        {
            return;
        }

        if ($this->currentRouteName === null || $this->currentRouteName === '')
        {
            $this->currentRouteName = $string;
        }
        else
        {
            if ($addSpace)
            {
                $this->currentRouteName .= ' ';
            }

            $this->currentRouteName .= $string;
        }
    }

    /**
     * @inheritDoc
     */
    public function prependToCurrentRouteName(string $string, bool $addSpace = true): void
    {
        if ($string === '')
        {
            return;
        }

        if ($this->currentRouteName === null || $this->currentRouteName === '')
        {
            $this->currentRouteName = $string;
        }
        else
        {
            if ($addSpace)
            {
                $this->currentRouteName = ' ' . $this->currentRouteName;
            }

            $this->currentRouteName = $string . $this->currentRouteName;
        }
    }
}