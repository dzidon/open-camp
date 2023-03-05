<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Sets and gets the current route name. Route names are stored in services.yaml under the "app_route_names" parameter.
 * The current route name is displayed in the title, breadcrumbs, and in the h1 heading.
 */
class RouteNamer implements RouteNamerInterface
{
    private string|null $currentRouteName = null;
    private string $siteName;
    private array $routeNames;

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, string $siteName, array $routeNames)
    {
        $this->requestStack = $requestStack;
        $this->siteName = $siteName;
        $this->routeNames = $routeNames;
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
        $fullPageName = $this->siteName;
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
        if (array_key_exists($route, $this->routeNames))
        {
            // has multiple variations
            if (is_array($this->routeNames[$route]))
            {
                // no variation specified, use the first one
                if ($variation === null || !array_key_exists($variation, $this->routeNames[$route]))
                {
                    $variation = array_key_first($this->routeNames[$route]);
                }

                $this->currentRouteName = $this->routeNames[$route][$variation];
            }
            else
            {
                $this->currentRouteName = $this->routeNames[$route];
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