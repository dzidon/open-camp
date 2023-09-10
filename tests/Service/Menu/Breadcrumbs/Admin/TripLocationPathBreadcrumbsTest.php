<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\TripLocationPath;
use App\Service\Menu\Breadcrumbs\Admin\TripLocationPathBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationPathBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private TripLocationPath $tripLocationPath;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private TripLocationPathBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-path/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($this->tripLocationPath);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($this->tripLocationPath);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($this->tripLocationPath);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->tripLocationPath = new TripLocationPath('Path');
        $reflectionClass = new ReflectionClass($this->tripLocationPath);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->tripLocationPath, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var TripLocationPathBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(TripLocationPathBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}