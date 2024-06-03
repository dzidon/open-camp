<?php

namespace App\Service\Menu\Breadcrumbs\User\ContactUs;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class ContactUsBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_contact_us';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'user_contact_us');
    }
}