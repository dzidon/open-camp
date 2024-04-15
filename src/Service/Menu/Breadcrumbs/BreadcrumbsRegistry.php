<?php

namespace App\Service\Menu\Breadcrumbs;

use App\Library\Menu\MenuType;
use App\Library\Menu\MenuTypeInterface;
use LogicException;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @inheritDoc
 */
class BreadcrumbsRegistry implements BreadcrumbsRegistryInterface
{
    /** @var BreadcrumbInterface[] */
    private array $breadcrumbs = [];

    /**
     * @inheritDoc
     */
    public function registerBreadcrumb(BreadcrumbInterface $breadcrumb): void
    {
        $supportedRoute = $breadcrumb->getSupportedRoute();

        if (array_key_exists($supportedRoute, $this->breadcrumbs))
        {
            throw new LogicException(
                sprintf('Your app contains multiple breadcrumb classes that support route "%s".', $supportedRoute)
            );
        }

        $this->breadcrumbs[$supportedRoute] = $breadcrumb;
    }

    /**
     * @inheritDoc
     */
    public function getBreadcrumbs(string $route, array $options = [], string $block = 'breadcrumbs'): MenuTypeInterface
    {
        $breadcrumbsToBuild = [];
        $menu = new MenuType('breadcrumbs', $block);
        $currentBreadcrumb = $this->getBreadcrumbForRoute($route);

        while ($currentBreadcrumb !== null)
        {
            $resolver = new OptionsResolver();
            $resolver->setIgnoreUndefined();

            $currentBreadcrumb->configureOptions($resolver);

            try
            {
                $resolvedOptions = $resolver->resolve($options);
            }
            catch (ExceptionInterface $exception)
            {
                $originalMessage = $exception->getMessage();
                $currentRoute = $currentBreadcrumb->getSupportedRoute();
                $newMessage = sprintf('An exception was thrown when building breadcrumbs for route "%s" on breadcrumb "%s": %s', $route, $currentRoute, $originalMessage);

                throw new LogicException(message: $newMessage, previous: $exception);
            }

            array_unshift($breadcrumbsToBuild, [
                'breadcrumb' => $currentBreadcrumb,
                'options'    => $resolvedOptions,
            ]);

            $previousRoute = $currentBreadcrumb->getPreviousRoute($resolvedOptions);

            if ($previousRoute === null)
            {
                $currentBreadcrumb = null;
            }
            else
            {
                $currentBreadcrumb = $this->getBreadcrumbForRoute($previousRoute);
            }
        }

        foreach ($breadcrumbsToBuild as $breadcrumbToBuild)
        {
            /** @var BreadcrumbInterface $breadcrumb */
            $breadcrumb = $breadcrumbToBuild['breadcrumb'];
            $options = $breadcrumbToBuild['options'];

            $breadcrumb->buildBreadcrumb($menu, $options);
        }

        $menuChildren = $menu->getChildren();

        if (!empty($menuChildren))
        {
            $keyLast = array_key_last($menuChildren);
            $menuChildLast = $menuChildren[$keyLast];
            $menuChildLast->setActive();
        }

        return $menu;
    }

    private function getBreadcrumbForRoute(string $route): BreadcrumbInterface
    {
        if (!array_key_exists($route, $this->breadcrumbs))
        {
            throw new LogicException(
                sprintf('There is no breadcrumb class for route "%s".', $route)
            );
        }

        return $this->breadcrumbs[$route];
    }
}