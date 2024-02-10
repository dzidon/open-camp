<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class PurchasableItemVariantBreadcrumbs extends AbstractBreadcrumbs implements PurchasableItemVariantBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildCreate(PurchasableItem $purchasableItem): MenuType
    {
        $purchasableItemId = $purchasableItem->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_create', ['id' => $purchasableItemId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(PurchasableItemVariant $purchasableItemVariant): MenuType
    {
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_read', ['id' => $purchasableItemVariantId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(PurchasableItemVariant $purchasableItemVariant): MenuType
    {
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_update', ['id' => $purchasableItemVariantId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(PurchasableItemVariant $purchasableItemVariant): MenuType
    {
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_delete', ['id' => $purchasableItemVariantId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}