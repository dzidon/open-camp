<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class LoginBreadcrumbs extends AbstractBreadcrumbs implements LoginBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function addLoginToMenuRegistry(): void
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_login')
            ->setActive()
        ;

        $this->registerBreadcrumbs($root);
    }
}