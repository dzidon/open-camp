<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\ApplicationToUserImportController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Application;

/**
 * Creates breadcrumbs for {@link ApplicationToUserImportController}.
 */
interface ApplicationToUserImportBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_application_import".
     *
     * @param Application $application
     * @return MenuTypeInterface
     */
    public function buildForApplicationImport(Application $application): MenuTypeInterface;
}