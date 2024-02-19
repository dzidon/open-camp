<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\Application;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class ApplicationToUserImportBreadcrumbs extends AbstractBreadcrumbs implements ApplicationToUserImportBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildForApplicationImport(Application $application): MenuType
    {
        $root = $this->createBreadcrumbs();
        $applicationId = $application->getId();

        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_camp_catalog');
        $this->addRoute($root, 'user_application_completed', [
            'applicationId' => $applicationId,
        ]);

        $this->addRoute($root, 'user_application_import')
            ->setActive()
        ;

        return $root;
    }
}