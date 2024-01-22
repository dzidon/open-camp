<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\Application;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class ApplicationBreadcrumbs extends AbstractBreadcrumbs implements ApplicationBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildForStepOneCreate(CampDate $campDate): MenuType
    {
        $root = $this->createRoot();
        $camp = $campDate->getCamp();

        $this->addCommonLinks($root, $camp);

        $campDateIdString = $campDate->getId()->toRfc4122();
        $text = $this->translator->trans('entity.application.singular');
        $this->addChildRoute($root, 'user_application_step_one_create', ['campDateId' => $campDateIdString])
            ->setText($text)
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildForStepOneUpdate(Application $application): MenuType
    {
        $root = $this->createRoot();
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        $this->addCommonLinks($root, $camp);

        $applicationIdString = $application->getId()->toRfc4122();
        $text = $this->translator->trans('entity.application.singular');
        $this->addChildRoute($root, 'user_application_step_one_update', ['applicationId' => $applicationIdString])
            ->setText($text)
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildForStepTwo(Application $application): MenuType
    {
        $root = $this->createRoot();
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        $this->addCommonLinks($root, $camp);

        $applicationIdString = $application->getId()->toRfc4122();
        $text = $this->translator->trans('entity.application.singular');
        $this->addChildRoute($root, 'user_application_step_two', ['applicationId' => $applicationIdString])
            ->setText($text)
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildForStepThree(Application $application): MenuType
    {
        $root = $this->createRoot();
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        $this->addCommonLinks($root, $camp);

        $applicationIdString = $application->getId()->toRfc4122();
        $text = $this->translator->trans('entity.application.singular');
        $this->addChildRoute($root, 'user_application_step_three', ['applicationId' => $applicationIdString])
            ->setText($text)
            ->setActive()
        ;

        return $root;
    }

    private function addCommonLinks(MenuType $root, ?Camp $camp): void
    {
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_camp_catalog');

        if ($camp === null)
        {
            return;
        }

        $campCategory = $camp->getCampCategory();
        $campCategories = [];

        if ($campCategory !== null)
        {
            $campCategories = $campCategory->getAncestors();
            $campCategories[] = $campCategory;
        }

        foreach ($campCategories as $key => $campCategory)
        {
            $path = $campCategory->getPath();
            $text = $campCategory->getName();

            $this->addChildRoute($root, 'user_camp_catalog', ['path' => $path], 'user_camp_catalog_' . $key)
                ->setText($text)
            ;
        }

        $text = $camp->getName();
        $this->addChildRoute($root, 'user_camp_detail', ['urlName' => $camp->getUrlName()])
            ->setText($text)
        ;
    }
}