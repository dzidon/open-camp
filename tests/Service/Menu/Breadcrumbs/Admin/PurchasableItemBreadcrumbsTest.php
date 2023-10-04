<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\PurchasableItem;
use App\Service\Menu\Breadcrumbs\Admin\PurchasableItemBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private PurchasableItem $purchasableItem;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private PurchasableItemBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_purchasable_item_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($this->purchasableItem);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($this->purchasableItem);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($this->purchasableItem);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->purchasableItem = new PurchasableItem('Item', 1000.0, 10);
        $reflectionClass = new ReflectionClass($this->purchasableItem);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->purchasableItem, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var PurchasableItemBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(PurchasableItemBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}