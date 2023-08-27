<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Service\Menu\Breadcrumbs\Admin\CampImageBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampImageBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private CampImageBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_image_list'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/images', $button->getUrl());
    }

    public function testUpload(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpload(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_image_list', 'admin_camp_image_upload'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/images', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_upload');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/upload-images', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'), UuidV4::fromString('a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_image_list', 'admin_camp_image_update'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/images', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-image/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'), UuidV4::fromString('a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_image_list', 'admin_camp_image_delete'],
            $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu)
        );

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camps', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/images', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_image_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-image/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var CampImageBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(CampImageBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}