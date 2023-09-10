<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\User\CampCatalogBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampCatalogBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private CampCatalogBreadcrumbs $breadcrumbs;

    public function testListWithEmptyCampCategories(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList([]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_camp_catalog'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/catalog', $button->getUrl());
    }

    public function testListWithCampCategories(): void
    {
        $campCategoryA = new CampCategory('A', 'a');
        $campCategoryB = new CampCategory('B', 'b');
        $campCategoryB->setParent($campCategoryA);

        $breadcrumbsMenu = $this->breadcrumbs->buildList([$campCategoryA, $campCategoryB]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_camp_catalog', 'user_camp_catalog_0', 'user_camp_catalog_1'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/catalog', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog_0');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/catalog/a', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog_1');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/catalog/a/b', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var CampCatalogBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(CampCatalogBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}