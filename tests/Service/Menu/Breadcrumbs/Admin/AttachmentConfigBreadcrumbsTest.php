<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\AttachmentConfig;
use App\Service\Menu\Breadcrumbs\Admin\AttachmentConfigBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class AttachmentConfigBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private AttachmentConfig $attachmentConfig;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private AttachmentConfigBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_attachment_config_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/attachment-configs', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_attachment_config_list', 'admin_attachment_config_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/attachment-configs', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/attachment-config/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($this->attachmentConfig);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_attachment_config_list', 'admin_attachment_config_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/attachment-configs', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/attachment-config/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($this->attachmentConfig);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_attachment_config_list', 'admin_attachment_config_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/attachment-configs', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/attachment-config/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($this->attachmentConfig);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_attachment_config_list', 'admin_attachment_config_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/attachment-configs', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_attachment_config_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/attachment-config/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->attachmentConfig = new AttachmentConfig('Config', 10.0);
        $reflectionClass = new ReflectionClass($this->attachmentConfig);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->attachmentConfig, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var AttachmentConfigBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(AttachmentConfigBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}