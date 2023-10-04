<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;

/**
 * Creates breadcrumbs for {@link }.
 */
interface PurchasableItemVariantBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_create".
     */
    public function buildCreate(PurchasableItem $purchasableItem): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_read".
     */
    public function buildRead(PurchasableItemVariant $purchasableItemVariant): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_update".
     */
    public function buildUpdate(PurchasableItemVariant $purchasableItemVariant): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_variant_delete".
     */
    public function buildDelete(PurchasableItemVariant $purchasableItemVariant): MenuTypeInterface;
}