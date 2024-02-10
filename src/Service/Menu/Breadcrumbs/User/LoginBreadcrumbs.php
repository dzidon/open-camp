<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

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
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_login')
            ->setActive()
        ;

        return $root;
    }
}