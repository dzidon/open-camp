<?php

namespace App\Tests\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\User\ProfileCamperBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileCamperBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private ProfileCamperBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_create'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbs->buildRead(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_read'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/1/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_update'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/1/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete(1);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_camper_list', 'user_profile_camper_delete'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/campers', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_camper_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/camper/1/delete', $button->getUrl());
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