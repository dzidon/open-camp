<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private TripLocationPath $tripLocationPath;
    private TripLocation $tripLocation;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_trip_location_create', [
            'trip_location_path' => $this->tripLocationPath,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_update', 'admin_trip_location_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/create-location', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_trip_location_read', [
            'trip_location_path' => $this->tripLocationPath,
            'trip_location'      => $this->tripLocation,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_update', 'admin_trip_location_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_trip_location_update', [
            'trip_location_path' => $this->tripLocationPath,
            'trip_location'      => $this->tripLocation,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_update', 'admin_trip_location_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_trip_location_delete', [
            'trip_location_path' => $this->tripLocationPath,
            'trip_location'      => $this->tripLocation,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_trip_location_path_list', 'admin_trip_location_path_update', 'admin_trip_location_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-paths', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_path_update');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/trip-location-path/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_trip_location_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/trip-location/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->tripLocationPath = new TripLocationPath('Path');
        $reflectionClass = new ReflectionClass($this->tripLocationPath);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->tripLocationPath, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $this->tripLocation = new TripLocation('Location', 1000.0, 10, $this->tripLocationPath);
        $reflectionClass = new ReflectionClass($this->tripLocation);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->tripLocation, UuidV4::fromString('a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}