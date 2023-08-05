<?php

namespace App\Tests\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\UserBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

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
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($uid);
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
        $this->assertSame('/admin/user/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($uid);
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
        $this->assertSame('/admin/user/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testUpdatePassword(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdatePassword($uid);
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
        $this->assertSame('/admin/user/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_user_update_password');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/user/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update/password', $button->getUrl());
    }

    public function testDelete(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($uid);
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
        $this->assertSame('/admin/user/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
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