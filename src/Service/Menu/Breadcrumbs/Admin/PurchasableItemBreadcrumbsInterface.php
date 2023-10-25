<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\PurchasableItemController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItem;

/**
 * Creates breadcrumbs for {@link PurchasableItemController}.
 */
interface PurchasableItemBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_read".
     */
    public function buildRead(PurchasableItem $purchasableItem): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_update".
     */
    public function buildUpdate(PurchasableItem $purchasableItem): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_purchasable_item_delete".
     */
    public function buildDelete(PurchasableItem $purchasableItem): MenuTypeInterface;
}