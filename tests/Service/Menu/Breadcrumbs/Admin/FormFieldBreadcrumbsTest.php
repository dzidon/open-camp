<?php

namespace App\Tests\Service\Menu\Breadcrumbs\Admin;

use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Service\Menu\Breadcrumbs\Admin\FormFieldBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class FormFieldBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private FormField $formField;

    private MenuTypeFactoryRegistryInterface $factoryRegistry;
    private FormFieldBreadcrumbs $breadcrumbs;

    public function testList(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildList();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_form_field_list'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_list');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/form-fields', $button->getUrl());
    }

    public function testCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_form_field_list', 'admin_form_field_create'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/form-fields', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_create');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/form-field/create', $button->getUrl());
    }

    public function testRead(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildRead($this->formField);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_form_field_list', 'admin_form_field_read'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/form-fields', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_read');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/form-field/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/read', $button->getUrl());
    }

    public function testUpdate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildUpdate($this->formField);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_form_field_list', 'admin_form_field_update'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/form-fields', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_update');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/form-field/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/update', $button->getUrl());
    }

    public function testDelete(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildDelete($this->formField);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame(['admin_home', 'admin_form_field_list', 'admin_form_field_delete'], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $button = $breadcrumbsMenu->getChild('admin_home');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_list');
        $this->assertSame(false, $button->isActive());
        $this->assertSame('/admin/form-fields', $button->getUrl());

        $button = $breadcrumbsMenu->getChild('admin_form_field_delete');
        $this->assertSame(true, $button->isActive());
        $this->assertSame('/admin/form-field/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b/delete', $button->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->formField = new FormField('Field', FormFieldTypeEnum::NUMBER, 'Field:');
        $reflectionClass = new ReflectionClass($this->formField);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($this->formField, UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->factoryRegistry = $menuTypeRegistry;

        /** @var FormFieldBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(FormFieldBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}