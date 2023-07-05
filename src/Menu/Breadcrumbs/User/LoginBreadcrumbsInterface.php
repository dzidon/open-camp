<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\LoginController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link LoginController}.
 */
interface LoginBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_login".
     *
     * @return MenuTypeInterface
     */
    public function buildLogin(): MenuTypeInterface;
}