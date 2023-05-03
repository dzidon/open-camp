<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;

/**
 * @inheritDoc
 */
class ProfileBreadcrumbs extends AbstractBreadcrumbs implements ProfileBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function initializeProfile(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_profile')
            ->setActive()
        ;

        $this->registerBreadcrumbs($root);
        return $root;
    }
}