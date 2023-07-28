<?php

namespace App\Tests\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\User\ProfileContactBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class ProfileContactBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private ProfileContactBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_create'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contact/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_read'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contact/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_update'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contact/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $uid = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($uid);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_delete'], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contact/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var ProfileContactBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileContactBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}