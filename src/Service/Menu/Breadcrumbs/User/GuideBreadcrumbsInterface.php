<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\GuideController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\User;

/**
 * Creates breadcrumbs for {@link GuideController}.
 */
interface GuideBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_guide_detail".
     */
    public function buildDetail(User $guide): MenuTypeInterface;
}