<?php

namespace App\Tests\Functional\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbs;
use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Tests\Functional\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests breadcrumbs of the admin profile controller.
 */
class ProfileBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    protected MenuTypeRegistryInterface $menuTypeRegistry;
    protected ProfileBreadcrumbs $breadcrumbs;

    /**
     * Tests the admin profile breadcrumbs.
     *
     * @return void
     * @throws Exception
     */
    public function testIndex(): void
    {
        $this->assertSame(null, $this->menuTypeRegistry->getMenuType('breadcrumbs'));

        $this->breadcrumbs->initializeIndex();
        $breadcrumbsMenu = $this->menuTypeRegistry->getMenuType('breadcrumbs');
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_profile'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('Dashboard', $homeButton->getText());
        $this->assertSame('/admin/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('admin_profile');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('Profile', $profileButton->getText());
        $this->assertSame('/admin/profile', $profileButton->getUrl());
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();

        /** @var MenuTypeRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}