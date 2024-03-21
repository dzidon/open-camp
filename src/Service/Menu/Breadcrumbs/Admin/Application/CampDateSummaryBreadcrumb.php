<?php

namespace App\Service\Menu\Breadcrumbs\Admin\Application;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampDateSummaryBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_camp_date_summary';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_camp_date_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var CampDate $campDate */
        $campDate = $options['camp_date'];

        $this->addRoute($breadcrumbs, 'admin_application_camp_date_summary', [
            'campDateId' => $campDate->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_date');
        $resolver->setAllowedTypes('camp_date', CampDate::class);
        $resolver->setRequired('camp_date');
    }
}