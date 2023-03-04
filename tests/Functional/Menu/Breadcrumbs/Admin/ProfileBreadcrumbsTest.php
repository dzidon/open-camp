<?php

namespace App\Tests\Functional\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbs;
use App\Tests\Functional\Menu\Breadcrumbs\AbstractBreadcrumbsTest;

/**
 * Tests breadcrumbs of the admin profile controller.
 */
class ProfileBreadcrumbsTest extends AbstractBreadcrumbsTest
{
    /**
     * Tests the admin profile breadcrumbs.
     *
     * @return void
     */
    public function testIndex(): void
    {
        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->getBreadcrumbs(ProfileBreadcrumbs::class);
        $menuTypeRegistry = $this->getMenuTypeRegistry();

        $this->assertSame(null, $menuTypeRegistry->getMenuType('breadcrumbs'));

        $breadcrumbs->initializeIndex();
        $breadcrumbsMenu = $menuTypeRegistry->getMenuType('breadcrumbs');
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_profile'], $this->getChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('Dashboard', $homeButton->getText());
        $this->assertSame('/admin/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('admin_profile');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('Profile', $profileButton->getText());
        $this->assertSame('/admin/profile', $profileButton->getUrl());
    }
}