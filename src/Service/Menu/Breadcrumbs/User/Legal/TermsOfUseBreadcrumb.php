<?php

namespace App\Service\Menu\Breadcrumbs\User\Legal;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;

class TermsOfUseBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_terms_of_use';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        $this->addRoute($breadcrumbs, 'user_terms_of_use');
    }
}