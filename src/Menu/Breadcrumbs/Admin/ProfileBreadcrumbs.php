<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class ProfileBreadcrumbs extends AbstractBreadcrumbs implements ProfileBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function initializeIndex(): void
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_profile')
            ->setActive()
        ;

        $this->registerBreadcrumbs($root);
    }
}