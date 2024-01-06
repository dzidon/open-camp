<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\ApplicationController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;

/**
 * Creates breadcrumbs for {@link ApplicationController}.
 */
interface ApplicationBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_application_step_one_create".
     *
     * @param CampDate $campDate
     * @return MenuTypeInterface
     */
    public function buildForStepOneCreate(CampDate $campDate): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_application_step_one_update".
     *
     * @param Application $application
     * @return MenuTypeInterface
     */
    public function buildForStepOneUpdate(Application $application): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_application_step_two".
     *
     * @param Application $application
     * @return MenuTypeInterface
     */
    public function buildForStepTwo(Application $application): MenuTypeInterface;
}