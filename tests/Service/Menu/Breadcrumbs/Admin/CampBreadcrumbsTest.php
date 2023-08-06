<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Service\Menu\Breadcrumbs\Admin\CampBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private CampBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_list'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_list', 'admin_camp_create'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_list', 'admin_camp_read'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_list', 'admin_camp_update'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_camp_list', 'admin_camp_delete'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var CampBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(CampBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}