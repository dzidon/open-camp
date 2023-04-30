<?php

namespace App\EventDispatcher\EventSubscriber;

use App\Service\RouteNamerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Automatically sets the current route name based on the "_route" attribute in the current request.
 *
 * @package App\EventSubscriber
 */
class RouteNameSubscriber
{
    private RouteNamerInterface $routeNamer;

    public function __construct(RouteNamerInterface $routeNamer)
    {
        $this->routeNamer = $routeNamer;
    }

    /**
     * Called before a controller action.
     *
     * @return void
     */
    #[AsEventListener(event: KernelEvents::CONTROLLER)]
    public function onKernelController(): void
    {
        $this->routeNamer->setCurrentRouteNameByRequest();
    }
}