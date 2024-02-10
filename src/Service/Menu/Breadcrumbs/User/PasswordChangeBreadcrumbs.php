<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class PasswordChangeBreadcrumbs extends AbstractBreadcrumbs implements PasswordChangeBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildPasswordChange(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_login');
        $this->addRoute($root, 'user_password_change')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildPasswordChangeComplete(string $token, bool $isAuthenticated): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');

        if ($isAuthenticated)
        {
            $this->addRoute($root, 'user_profile_password_change_create');
        }
        else
        {
            $this->addRoute($root, 'user_login');
            $this->addRoute($root, 'user_password_change');
        }

        $this->addRoute($root, 'user_password_change_complete', ['token' => $token])
            ->setActive()
        ;

        return $root;
    }
}