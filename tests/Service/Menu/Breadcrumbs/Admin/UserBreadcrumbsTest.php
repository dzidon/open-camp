<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\Admin\UserBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class UserBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private User $user;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private UserBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($this->user);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($this->user);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdatePassword($this->user);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_update', 'admin_user_update_password'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($this->user);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_user_list', 'admin_user_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $container = static::getContainer();

        $this->user = new User('bob@gmail.com');
        $reflectionClass = new ReflectionClass($this->user);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->user, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var UserBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(UserBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}