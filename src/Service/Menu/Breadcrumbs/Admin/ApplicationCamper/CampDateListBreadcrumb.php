<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationCamper;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampDateListBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_camp_date_application_camper_list';
    }

    public function getPreviousRoute(array $options): ?string
    {
        /** @var null|CampDate $campDate */
        $campDate = $options['camp_date'];

        if ($campDate === null)
        {
            return 'admin_application_camp_list';
        }

        return 'admin_application_camp_date_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var null|CampDate $campDate */
        $campDate = $options['camp_date'];

        $this->addRoute($breadcrumbs, 'admin_camp_date_application_camper_list', [
            'campDateId' => $campDate?->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_date');
        $resolver->setAllowedTypes('camp_date', ['null', CampDate::class]);
        $resolver->setRequired('camp_date');
    }
}