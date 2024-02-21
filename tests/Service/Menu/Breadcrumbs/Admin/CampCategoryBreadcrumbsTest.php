<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampCategoryBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private CampCategory $campCategory;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_category_list');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_category_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-categories', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_category_create');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_category_list', 'admin_camp_category_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp-categories', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-category/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_category_read', [
            'camp_category' => $this->campCategory,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_category_list', 'admin_camp_category_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp-categories', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-category/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_category_update', [
            'camp_category' => $this->campCategory,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_category_list', 'admin_camp_category_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp-categories', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-category/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_category_delete', [
            'camp_category' => $this->campCategory,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_category_list', 'admin_camp_category_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp-categories', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_category_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-category/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->campCategory = new CampCategory('Name', 'name');
        $reflectionClass = new ReflectionClass($this->campCategory);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->campCategory, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}