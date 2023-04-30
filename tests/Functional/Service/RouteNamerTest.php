<?php

namespace App\Tests\Functional\Service;

use App\Service\RouteNamer;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $routeNamer = $this->createRouteNamer(null);
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
        $routeNamer = $this->createRouteNamer(null);
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
        $routeNamer = $this->createRouteNamer(null);
        $this->assertSame('Site Name', $routeNamer->getCurrentTitle());

        $routeNamer->setCurrentRouteName('Route Name');
        $this->assertSame('Route Name - Site Name', $routeNamer->getCurrentTitle());
    }

    /**
     * Tests that the current route name is set correctly using the "_route" request attribute.
     *
     * @return void
     * @throws Exception
     */
    public function testSetCurrentRouteNameByRequest(): void
    {
        $routeNamer = $this->createRouteNamer('product_overview');
        $routeNamer->setCurrentRouteNameByRequest();
        $this->assertSame('Products', $routeNamer->getCurrentRouteName());

        $routeNamer = $this->createRouteNamer('user');
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
     * @throws Exception
     */
    public function testSetCurrentRouteNameByRoute(): void
    {
        $routeNamer = $this->createRouteNamer(null);
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
     * @throws Exception
     */
    public function testAppendToCurrentRouteName(): void
    {
        $routeNamer = $this->createRouteNamer(null);
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
        $routeNamer = $this->createRouteNamer(null);
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
     * Returns an instance of the RouteNamer service.
     *
     * @param string|null $requestRoute Route identifier used as the request's "_route" attribute.
     * @return RouteNamer
     * @throws Exception
     */
    private function createRouteNamer(null|string $requestRoute): RouteNamer
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);
        $requestStack->push(new Request([], [], [
            '_route' => $requestRoute
        ]));

        /** @var TranslatorInterface|MockObject $translatorMock */
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function (string $id)
            {
                $catalog = [
                    'app.site_name'          => 'Site Name',
                    'route.product_overview' => 'Products',
                    'route.user.new'         => 'New user',
                    'route.user.existing'    => 'Existing user',
                ];

                return $catalog[$id];
            })
        ;

        // config
        $routeTransKeys = [
            'product_overview' => 'route.product_overview',
            'user' => [
                'new'      => 'route.user.new',
                'existing' => 'route.user.existing',
            ],
        ];

        return new RouteNamer($requestStack, $translatorMock, $routeTransKeys);
    }
}