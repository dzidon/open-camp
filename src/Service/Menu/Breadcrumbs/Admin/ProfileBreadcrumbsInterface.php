<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\ProfileController;
use App\Library\Menu\MenuTypeInterface;

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