<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\User\RegistrationBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegistrationBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    private RegistrationBreadcrumbs $breadcrumbs;

    public function testBuildRegistration(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRegistration();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_registration',
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_registration');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/registration', $profileButton->getUrl());
    }

    public function testBuildRegistrationComplete(): void
    {
        $token = 'xyz';
        $breadcrumbsMenu = $this->breadcrumbs->buildRegistrationComplete($token);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_registration',
            'user_registration_complete',
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_registration');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/registration', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_registration_complete');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $expectedUrl = sprintf('/registration/complete/%s', $token);
        $this->assertSame($expectedUrl, $userPasswordChangeButton->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var RegistrationBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(RegistrationBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}