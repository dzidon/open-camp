<?php

namespace App\Tests\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\Admin\RoleBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class RoleBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private RoleBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_create'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/role/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_read'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/role/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_update'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/role/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_delete'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/role/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var RoleBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(RoleBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}