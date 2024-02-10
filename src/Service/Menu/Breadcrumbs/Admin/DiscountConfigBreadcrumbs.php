<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\DiscountConfig;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class DiscountConfigBreadcrumbs extends AbstractBreadcrumbs implements DiscountConfigBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_discount_config_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_discount_config_list');
        $this->addRoute($root, 'admin_discount_config_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(DiscountConfig $discountConfig): MenuType
    {
        $discountConfigId = $discountConfig->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_discount_config_list');
        $this->addRoute($root, 'admin_discount_config_read', ['id' => $discountConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(DiscountConfig $discountConfig): MenuType
    {
        $discountConfigId = $discountConfig->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_discount_config_list');
        $this->addRoute($root, 'admin_discount_config_update', ['id' => $discountConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(DiscountConfig $discountConfig): MenuType
    {
        $discountConfigId = $discountConfig->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_discount_config_list');
        $this->addRoute($root, 'admin_discount_config_delete', ['id' => $discountConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}