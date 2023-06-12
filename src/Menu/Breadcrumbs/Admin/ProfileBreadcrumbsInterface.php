<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\ProfileController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link ProfileController}.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_profile".
     */
    public function buildProfile(): MenuTypeInterface;
}