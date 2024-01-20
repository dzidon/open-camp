<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\DiscountConfigController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\DiscountConfig;

/**
 * Creates breadcrumbs for {@link DiscountConfigController}.
 */
interface DiscountConfigBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_discount_config_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_discount_config_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_discount_config_read".
     */
    public function buildRead(DiscountConfig $discountConfig): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_discount_config_update".
     */
    public function buildUpdate(DiscountConfig $discountConfig): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_discount_config_delete".
     */
    public function buildDelete(DiscountConfig $discountConfig): MenuTypeInterface;
}