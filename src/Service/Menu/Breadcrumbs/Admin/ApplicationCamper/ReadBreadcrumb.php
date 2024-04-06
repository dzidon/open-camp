<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationCamper;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\ApplicationCamper;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_camper_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        /** @var bool $isParentCampDateList */
        $isParentCampDateList = $options['is_parent_camp_date_list'];

        if ($isParentCampDateList)
        {
            return 'admin_camp_date_application_camper_list';
        }

        return 'admin_application_camper_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var ApplicationCamper $applicationCamper */
        $applicationCamper = $options['application_camper'];

        $this->addRoute($breadcrumbs, 'admin_application_camper_read', [
            'id' => $applicationCamper->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application_camper');
        $resolver->setAllowedTypes('application_camper', ApplicationCamper::class);
        $resolver->setRequired('application_camper');

        $resolver->setDefined('is_parent_camp_date_list');
        $resolver->setAllowedTypes('is_parent_camp_date_list', 'bool');
        $resolver->setRequired('is_parent_camp_date_list');
        $resolver->setDefault('is_parent_camp_date_list', false);
    }
}