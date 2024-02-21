<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemVariantBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private PurchasableItemVariant $purchasableItemVariant;
    private PurchasableItem $purchasableItem;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_purchasable_item_variant_create', [
            'purchasable_item' => $this->purchasableItem,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_update', 'admin_purchasable_item_variant_create'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_variant_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/create-variant', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_purchasable_item_variant_read', [
            'purchasable_item'         => $this->purchasableItem,
            'purchasable_item_variant' => $this->purchasableItemVariant,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_update', 'admin_purchasable_item_variant_read'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_variant_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item-variant/b1f5a345-9f80-4949-a692-16adc4f7a05a/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_purchasable_item_variant_update', [
            'purchasable_item'         => $this->purchasableItem,
            'purchasable_item_variant' => $this->purchasableItemVariant,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_update', 'admin_purchasable_item_variant_update'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_variant_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item-variant/b1f5a345-9f80-4949-a692-16adc4f7a05a/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_purchasable_item_variant_delete', [
            'purchasable_item'         => $this->purchasableItem,
            'purchasable_item_variant' => $this->purchasableItemVariant,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_purchasable_item_list', 'admin_purchasable_item_update', 'admin_purchasable_item_variant_delete'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-items', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_purchasable_item_variant_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/purchasable-item-variant/b1f5a345-9f80-4949-a692-16adc4f7a05a/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $reflectionClass = new ReflectionClass($this->purchasableItem);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->purchasableItem, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $this->purchasableItem);
        $reflectionClass = new ReflectionClass($this->purchasableItemVariant);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->purchasableItemVariant, UuidV4::fromString('b1f5a345-9f80-4949-a692-16adc4f7a05a'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}