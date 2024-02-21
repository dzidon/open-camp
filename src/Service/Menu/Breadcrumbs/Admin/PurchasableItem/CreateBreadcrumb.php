<?php

namespace App\Service\Menu\Breadcrumbs\Admin\PurchasableItem;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_purchasable_item_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_purchasable_item_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'admin_purchasable_item_create');
    }
}