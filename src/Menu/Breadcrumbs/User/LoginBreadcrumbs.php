<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;

/**
 * @inheritDoc
 */
class LoginBreadcrumbs extends AbstractBreadcrumbs implements LoginBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildLogin(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_login')
            ->setActive()
        ;

        return $root;
    }
}