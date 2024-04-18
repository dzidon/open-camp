<?php

namespace App\Service\Menu\Breadcrumbs\Admin\GalleryImageCategory;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_gallery_image_category_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_gallery_image_category_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'admin_gallery_image_category_create');
    }
}