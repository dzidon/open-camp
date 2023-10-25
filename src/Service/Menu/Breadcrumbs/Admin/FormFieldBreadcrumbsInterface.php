<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\FormField;
use App\Model\Entity\PurchasableItem;

/**
 * Creates breadcrumbs for {@link }.
 */
interface FormFieldBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_form_field_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_form_field_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_form_field_read".
     */
    public function buildRead(FormField $formField): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_form_field_update".
     */
    public function buildUpdate(FormField $formField): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_form_field_delete".
     */
    public function buildDelete(FormField $formField): MenuTypeInterface;
}