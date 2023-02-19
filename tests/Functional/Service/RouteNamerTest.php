<?php

namespace App\Tests\Functional\Service;

use App\Service\RouteNamer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tests the route namer.
 */
class RouteNamerTest extends TestCase
{
    const TEST_ROUTES = [
        'product_overview' => 'Products',
        'user' => [
            'new' => 'New user',
            'existing' => 'Existing user',
        ],
    ];

    /**
     * Tests that "isCurrentRouteNameSet" returns the right boolean values.
     *
     * @return void
     */
    public function testIsCurrentRouteNameSet(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
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
     */
    public function testSetAndGetCurrentRouteName(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
        $routeNamer->setCurrentRouteName('Route Name');
        $this->assertSame('Route Name', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests that the current page title is set correctly.
     *
     * @return void
     */
    public function testGetCurrentTitle(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
        $this->assertSame('Site Name', $routeNamer->getCurrentTitle());

        $routeNamer->setCurrentRouteName('Route Name');
        $this->assertSame('Route Name - Site Name', $routeNamer->getCurrentTitle());
    }

    /**
     * Tests that the current route name is set correctly using the "_route" request attribute.
     *
     * @return void
     */
    public function testSetCurrentRouteNameByRequest(): void
    {
        $routeNamer = $this->getMockedRouteNamer('product_overview');
        $routeNamer->setCurrentRouteNameByRequest();
        $this->assertSame('Products', $routeNamer->getCurrentRouteName());

        $routeNamer = $this->getMockedRouteNamer('user');
        $routeNamer->setCurrentRouteNameByRequest();
        $this->assertSame('New user', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteNameByRequest('existing');
        $this->assertSame('Existing user', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteNameByRequest('nonexistent');
        $this->assertSame('New user', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests that the current route name is set correctly using the route identifier and its variation (if it has any).
     *
     * @return void
     */
    public function testSetCurrentRouteNameByRoute(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
        $routeNamer->setCurrentRouteNameByRoute('nonexistent');
        $this->assertSame(null, $routeNamer->getCurrentRouteName());
        $this->assertSame('Site Name', $routeNamer->getCurrentTitle());

        $routeNamer->setCurrentRouteNameByRoute('product_overview');
        $this->assertSame('Products', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteNameByRoute('user');
        $this->assertSame('New user', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteNameByRoute('user', 'existing');
        $this->assertSame('Existing user', $routeNamer->getCurrentRouteName());

        $routeNamer->setCurrentRouteNameByRoute('user', 'nonexistent');
        $this->assertSame('New user', $routeNamer->getCurrentRouteName());
    }

    /**
     * Tests appending to the current route name.
     *
     * @return void
     */
    public function testAppendToCurrentRouteName(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
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
     */
    public function testPrependToCurrentRouteName(): void
    {
        $routeNamer = $this->getMockedRouteNamer(null);
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
     * Returns an instance of RouteNamer which uses mocked dependencies.
     *
     * @param string|null $requestRoute Route identifier used as the request's "_route" attribute.
     * @return RouteNamer
     */
    private function getMockedRouteNamer(null|string $requestRoute): RouteNamer
    {
        // parameter bag mock
        $parameterBag = $this->getMockBuilder(ParameterBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $parameterBag->expects($this->any())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnCallback(function($argument) {
                if ($argument === 'app_route_names')
                {
                    return RouteNamerTest::TEST_ROUTES;
                }
                else if ($argument === 'app_site_name')
                {
                    return 'Site Name';
                }

                return null;
            }))
        ;

        // request stack mock
        $requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn(new Request([], [], [
                '_route' => $requestRoute
            ]))
        ;

        return new RouteNamer($requestStack, $parameterBag);
    }
}