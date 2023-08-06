<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\User\ProfileCamperBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class ProfileCamperBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private ProfileCamperBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var ProfileCamperBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileCamperBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}