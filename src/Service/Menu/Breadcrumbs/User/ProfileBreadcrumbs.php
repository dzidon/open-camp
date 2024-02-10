<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class ProfileBreadcrumbs extends AbstractBreadcrumbs implements ProfileBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildBilling(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_billing')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildPasswordChange(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_password_change')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildPasswordChangeCreate(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_password_change_create')
            ->setActive()
        ;

        return $root;
    }
}