<?php

namespace App\Service\Menu\Breadcrumbs\Admin\GalleryImage;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class ListBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_gallery_image_list';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'admin_gallery_image_list');
    }
}