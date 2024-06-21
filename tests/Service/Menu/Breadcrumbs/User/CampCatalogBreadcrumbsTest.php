<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampCatalogBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testListWithEmptyCampCategories(): void
    {
        $menu = $this->breadcrumbsRegistry->getBreadcrumbs('user_camp_catalog');
        $menuItemIdentifiers = $this->getTreeNodeChildrenIdentifiers($menu);

        $this->assertSame('breadcrumbs', $menu->getIdentifier());
        $this->assertSame(['user_home', 'user_camp_catalog'], $menuItemIdentifiers);

        $button = $menu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $menu->getChild('user_camp_catalog');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/camps', $button->getUrl());
    }

    public function testListWithCampCategories(): void
    {
        $campCategoryA = new CampCategory('A', 'a', 100);
        $campCategoryB = new CampCategory('B', 'b', 100);
        $campCategoryB->setParent($campCategoryA);

        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_camp_catalog', [
            'camp_category' => $campCategoryB,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_camp_catalog', 'user_camp_catalog_0', 'user_camp_catalog_1'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog_0');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/camps/a', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_camp_catalog_1');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/camps/a/b', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}