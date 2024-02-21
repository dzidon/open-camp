<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class ProfileContactBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private Contact $contact;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_profile_contact_list');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('user_profile_contact_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/profile/contacts', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_profile_contact_create');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_profile_contact_read', [
            'contact' => $this->contact,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_profile_contact_update', [
            'contact' => $this->contact,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_profile_contact_delete', [
            'contact' => $this->contact,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['user_home', 'user_profile_contact_list', 'user_profile_contact_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

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
        $container = static::getContainer();

        $user = new User('bob@gmail.com');
        $this->contact = new Contact('David', 'Smith', $user, ContactRoleEnum::FATHER);
        $reflectionClass = new ReflectionClass($this->contact);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->contact, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}