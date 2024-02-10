<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class PurchasableItemVariantValueBreadcrumbs extends AbstractBreadcrumbs implements PurchasableItemVariantValueBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildCreate(PurchasableItemVariant $purchasableItemVariant): MenuType
    {
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_update', ['id' => $purchasableItemVariantId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_value_create', ['id' => $purchasableItemVariantId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(PurchasableItemVariantValue $purchasableItemVariantValue): MenuType
    {
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();
        $purchasableItemVariantValueId = $purchasableItemVariantValue->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_update', ['id' => $purchasableItemVariantId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_value_read', ['id' => $purchasableItemVariantValueId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(PurchasableItemVariantValue $purchasableItemVariantValue): MenuType
    {
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();
        $purchasableItemVariantValueId = $purchasableItemVariantValue->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_update', ['id' => $purchasableItemVariantId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_value_update', ['id' => $purchasableItemVariantValueId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(PurchasableItemVariantValue $purchasableItemVariantValue): MenuType
    {
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemId = $purchasableItem->getId();
        $purchasableItemVariantId = $purchasableItemVariant->getId();
        $purchasableItemVariantValueId = $purchasableItemVariantValue->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_purchasable_item_list');
        $this->addRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_update', ['id' => $purchasableItemVariantId->toRfc4122()]);
        $this->addRoute($root, 'admin_purchasable_item_variant_value_delete', ['id' => $purchasableItemVariantValueId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}