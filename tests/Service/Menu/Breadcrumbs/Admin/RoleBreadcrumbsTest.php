<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\Role;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class RoleBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private Role $role;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_role_list');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_role_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/roles', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_role_create');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_role_read', [
            'role' => $this->role,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_role_update', [
            'role' => $this->role,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_role_delete', [
            'role' => $this->role,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_role_list', 'admin_role_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $container = static::getContainer();

        $this->role = new Role('Role');
        $reflectionClass = new ReflectionClass($this->role);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->role, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}