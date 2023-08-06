<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\User\LoginBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoginBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    private LoginBreadcrumbs $breadcrumbs;

    public function testLogin(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildLogin();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_login'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_login');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/login', $profileButton->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var LoginBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(LoginBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}