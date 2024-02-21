<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\AttachmentConfig;
use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class AttachmentConfigBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private AttachmentConfig $attachmentConfig;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_attachment_config_list');

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_attachment_config_create');

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_attachment_config_read', [
            'attachment_config' => $this->attachmentConfig,
        ]);

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_attachment_config_update', [
            'attachment_config' => $this->attachmentConfig,
        ]);

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
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('admin_attachment_config_delete', [
            'attachment_config' => $this->attachmentConfig,
        ]);

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

        $this->attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $reflectionClass = new ReflectionClass($this->attachmentConfig);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->attachmentConfig, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}