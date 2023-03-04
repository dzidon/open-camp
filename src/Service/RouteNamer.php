<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Sets and gets the current route name. Route names are stored in services.yaml under the "app_route_names" parameter.
 * The current route name is displayed in the title, breadcrumbs, and in the h1 heading.
 */
class RouteNamer implements RouteNamerInterface
{
    private string|null $currentRouteName = null;

    private RequestStack $requestStack;
    private ParameterBagInterface $parameterBag;

    public function __construct(RequestStack $requestStack, ParameterBagInterface $parameterBag)
    {
        $this->requestStack = $requestStack;
        $this->parameterBag = $parameterBag;
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
        $fullPageName = $this->parameterBag->get('app_site_name');
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
        $routeNames = $this->parameterBag->get('app_route_names');
        if ($routeNames !== null && array_key_exists($route, $routeNames))
        {
            // has multiple variations
            if (is_array($routeNames[$route]))
            {
                // no variation specified, use the first one
                if ($variation === null || !array_key_exists($variation, $routeNames[$route]))
                {
                    $variation = array_key_first($routeNames[$route]);
                }

                $this->currentRouteName = $routeNames[$route][$variation];
            }
            else
            {
                $this->currentRouteName = $routeNames[$route];
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