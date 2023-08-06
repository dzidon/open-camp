<?php

namespace App\Service\Routing;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sets and gets the current route name. The current route name is displayed in the title, and in the h1 heading.
 */
class RouteNamer implements RouteNamerInterface
{
    private string|null $currentRouteName = null;

    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
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
    public function setCurrentRouteNameByRequest(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');

        if (is_string($route))
        {
            $this->setCurrentRouteNameByRoute($route);
        }
    }

    /**
     * @inheritDoc
     */
    public function setCurrentRouteNameByRoute(string $route): void
    {
        $this->currentRouteName = $this->translator->trans("route.$route");
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