<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\PurchasableItemVariantValueController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;

/**
 * Creates breadcrumbs for {@link PurchasableItemVariantValueController}.
 */
interface PurchasableItemVariantValueBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_value_create".
     */
    public function buildCreate(PurchasableItemVariant $purchasableItemVariant): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_value_read".
     */
    public function buildRead(PurchasableItemVariantValue $purchasableItemVariantValue): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_value_update".
     */
    public function buildUpdate(PurchasableItemVariantValue $purchasableItemVariantValue): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_value_delete".
     */
    public function buildDelete(PurchasableItemVariantValue $purchasableItemVariantValue): MenuTypeInterface;
}