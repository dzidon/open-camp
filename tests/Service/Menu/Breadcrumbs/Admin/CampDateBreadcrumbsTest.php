<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use DateTimeImmutable;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private Camp $camp;
    private CampDate $campDate;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_date_list', [
            'camp' => $this->camp,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_date_list'],
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

        $button = $breadcrumbsMenu->getChild('admin_camp_date_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/dates', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_date_create', [
            'camp' => $this->camp,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_date_list', 'admin_camp_date_create'],
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

        $button = $breadcrumbsMenu->getChild('admin_camp_date_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/dates', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_date_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/create-date', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_date_read', [
            'camp'      => $this->camp,
            'camp_date' => $this->campDate
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_date_list', 'admin_camp_date_read'],
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

        $button = $breadcrumbsMenu->getChild('admin_camp_date_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/dates', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_date_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-date/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_date_update', [
            'camp'      => $this->camp,
            'camp_date' => $this->campDate
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_date_list', 'admin_camp_date_update'],
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

        $button = $breadcrumbsMenu->getChild('admin_camp_date_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/dates', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_date_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-date/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_camp_date_delete', [
            'camp'      => $this->camp,
            'camp_date' => $this->campDate
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(
            ['admin_home', 'admin_camp_list', 'admin_camp_update', 'admin_camp_date_list', 'admin_camp_date_delete'],
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

        $button = $breadcrumbsMenu->getChild('admin_camp_date_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/dates', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_camp_date_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/camp-date/a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->camp = new Camp('Camp', 'camp', 5, 10, 321);
        $reflectionClass = new ReflectionClass($this->camp);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->camp, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $this->campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 100.0, 200.0, 10, $this->camp);
        $reflectionClass = new ReflectionClass($this->campDate);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->campDate, UuidV4::fromString('a37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}