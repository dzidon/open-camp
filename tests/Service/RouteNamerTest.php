<?php

namespace App\Tests\Service;

use App\Service\RouteNamer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tests the route namer.
 */
class RouteNamerTest extends KernelTestCase
{
    /**
     * Tests that the method "isCurrentRouteNameSet" returns the right boolean values.
     *
     * @return void
     * @throws Exception
     */
    public function testIsCurrentRouteNameSet(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $routeNamer->setCurrentRouteName(null);
        $this->assertSame(false, $routeNamer->isCurrentRouteNameSet());

        $routeNamer->setCurrentRouteName('');
        $this->assertSame(false, $routeNamer->isCurrentRouteNameSet());

        $routeNamer->setCurrentRouteName('A');
        $this->assertSame(true, $routeNamer->isCurrentRouteNameSet());
    }

    /**
     * Tests the current route name setter and getter.
     *
     * @return void
     * @throws Exception
     */
    public function testSetAndGetCurrentRouteName(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $routeNamer->setCurrentRouteName('Route Name');
        $this->assertSame('Route Name', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests that the current page title is set correctly.
     *
     * @return void
     * @throws Exception
     */
    public function testGetCurrentTitle(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $this->assertSame('app.site_name', $routeNamer->getCurrentTitle());

        $routeNamer->setCurrentRouteName('Name');
        $this->assertSame('Name - app.site_name', $routeNamer->getCurrentTitle());
    }

    /**
     * Tests that the current route name is set correctly using the "_route" request attribute.
     *
     * @return void
     * @throws Exception
     */
    public function testSetCurrentRouteNameByRequest(): void
    {
        $routeNamer = $this->getRouteNamer('user_home');
        $routeNamer->setCurrentRouteNameByRequest();
        $this->assertSame('route.user_home', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests that the current route name is set correctly using the route identifier.
     *
     * @return void
     * @throws Exception
     */
    public function testSetCurrentRouteNameByRoute(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $routeNamer->setCurrentRouteNameByRoute('user_home');
        $this->assertSame('route.user_home', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests appending to the current route name.
     *
     * @return void
     * @throws Exception
     */
    public function testAppendToCurrentRouteName(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->appendToCurrentRouteName('');
        $this->assertSame('Name', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName(null);
        $routeNamer->appendToCurrentRouteName('A', false);
        $this->assertSame('A', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName(null);
        $routeNamer->appendToCurrentRouteName('A');
        $this->assertSame('A', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->appendToCurrentRouteName('A', false);
        $this->assertSame('NameA', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->appendToCurrentRouteName('A');
        $this->assertSame('Name A', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests prepending to the current route name.
     *
     * @return void
     * @throws Exception
     */
    public function testPrependToCurrentRouteName(): void
    {
        $routeNamer = $this->getRouteNamer(null);
        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->prependToCurrentRouteName('');
        $this->assertSame('Name', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName(null);
        $routeNamer->prependToCurrentRouteName('A', false);
        $this->assertSame('A', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName(null);
        $routeNamer->prependToCurrentRouteName('A');
        $this->assertSame('A', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->prependToCurrentRouteName('A', false);
        $this->assertSame('AName', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteName('Name');
        $routeNamer->prependToCurrentRouteName('A');
        $this->assertSame('A Name', $routeNamer->getCurrentRouteName());
    }

    /**
     * Gets an instance of the RouteNamer from the service container.
     *
     * @param string|null $requestRoute Route identifier used as the request's "_route" attribute.
     * @return RouteNamer
     * @throws Exception
     */
    private function getRouteNamer(null|string $requestRoute): RouteNamer
    {
        $container = static::getContainer();

        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);
        $requestStack->push(new Request([], [], [
            '_route' => $requestRoute
        ]));

        /** @var RouteNamer $routeNamer */
        $routeNamer = $container->get(RouteNamer::class);

        return $routeNamer;
    }
}