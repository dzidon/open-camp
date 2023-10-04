<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\PurchasableItem;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class PurchasableItemBreadcrumbs extends AbstractBreadcrumbs implements PurchasableItemBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_purchasable_item_list')
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
        $this->addChildRoute($root, 'admin_purchasable_item_list');
        $this->addChildRoute($root, 'admin_purchasable_item_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(PurchasableItem $purchasableItem): MenuType
    {
        $purchasableItemId = $purchasableItem->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_purchasable_item_list');
        $this->addChildRoute($root, 'admin_purchasable_item_read', ['id' => $purchasableItemId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(PurchasableItem $purchasableItem): MenuType
    {
        $purchasableItemId = $purchasableItem->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_purchasable_item_list');
        $this->addChildRoute($root, 'admin_purchasable_item_update', ['id' => $purchasableItemId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(PurchasableItem $purchasableItem): MenuType
    {
        $purchasableItemId = $purchasableItem->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_purchasable_item_list');
        $this->addChildRoute($root, 'admin_purchasable_item_delete', ['id' => $purchasableItemId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}