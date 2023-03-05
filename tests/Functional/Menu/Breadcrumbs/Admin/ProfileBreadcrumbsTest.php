<?php

namespace App\Tests\Functional\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbs;
use App\Tests\Functional\Menu\Breadcrumbs\BreadcrumbsTestCase;
use Exception;

/**
 * Tests breadcrumbs of the admin profile controller.
 */
class ProfileBreadcrumbsTest extends BreadcrumbsTestCase
{
    private ProfileBreadcrumbs $breadcrumbs;

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

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}