<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\FormField;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class FormFieldBreadcrumbs extends AbstractBreadcrumbs implements FormFieldBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_form_field_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_form_field_list');
        $this->addChildRoute($root, 'admin_form_field_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(FormField $formField): MenuType
    {
        $formFieldId = $formField->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_form_field_list');
        $this->addChildRoute($root, 'admin_form_field_read', ['id' => $formFieldId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(FormField $formField): MenuType
    {
        $formFieldId = $formField->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_form_field_list');
        $this->addChildRoute($root, 'admin_form_field_update', ['id' => $formFieldId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(FormField $formField): MenuType
    {
        $formFieldId = $formField->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_form_field_list');
        $this->addChildRoute($root, 'admin_form_field_delete', ['id' => $formFieldId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}