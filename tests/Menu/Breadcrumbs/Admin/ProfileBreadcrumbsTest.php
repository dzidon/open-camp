<?php

namespace App\Tests\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private ProfileBreadcrumbs $breadcrumbs;

    public function testProfile(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildProfile();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_profile'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/admin', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('admin_profile');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/admin/profile', $profileButton->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}