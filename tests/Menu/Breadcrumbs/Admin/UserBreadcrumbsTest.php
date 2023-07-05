<?php

namespace App\Tests\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\UserBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private UserBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_create'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRead(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_read'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/1/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_update'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/1/update', $button->getUrl());
    }

    public function testUpdatePassword(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdatePassword(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_update', 'admin_user_update_password'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/user/1/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_update_password');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/1/update/password', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_delete'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/users', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/1/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var UserBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(UserBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}