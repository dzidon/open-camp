<?php

namespace App\Service\Menu\Breadcrumbs\User\Profile\Camper;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_profile_camper_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_profile_camper_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'user_profile_camper_create');
    }
}