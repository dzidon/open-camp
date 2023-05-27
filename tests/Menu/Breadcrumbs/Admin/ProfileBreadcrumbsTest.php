<?php

namespace App\Tests\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests breadcrumbs of the admin profile controller.
 */
class ProfileBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    protected MenuTypeFactoryRegistryInterface $factoryRegistry;
    protected ProfileBreadcrumbs $breadcrumbs;

    /**
     * Tests the admin profile breadcrumbs.
     *
     * @return void
     * @throws Exception
     */
    public function testProfile(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildProfile();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_profile'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/admin/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('admin_profile');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/admin/profile/', $profileButton->getUrl());
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