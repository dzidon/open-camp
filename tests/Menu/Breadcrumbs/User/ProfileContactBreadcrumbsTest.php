<?php

namespace App\Tests\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\User\ProfileContactBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        $breadcrumbsMenu = $this->breadcrumbs->buildRead(1);
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
        $this->assertSame('/profile/contact/1/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate(1);
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
        $this->assertSame('/profile/contact/1/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete(1);
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
        $this->assertSame('/profile/contact/1/delete', $button->getUrl());
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