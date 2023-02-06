<?php

namespace App\Twig;

use App\Service\RouteNamerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds route name related functions to Twig.
 *
 * @package App\Twig
 */
class RouteNameExtension extends AbstractExtension
{
    private RouteNamerInterface $routeNamer;

    public function __construct(RouteNamerInterface $routeNamer)
    {
        $this->routeNamer = $routeNamer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_name', [$this->routeNamer, 'getCurrentRouteName']),
            new TwigFunction('page_title', [$this->routeNamer, 'getCurrentTitle']),
            new TwigFunction('is_route_name_set', [$this->routeNamer, 'isCurrentRouteNameSet']),
        ];
    }
}