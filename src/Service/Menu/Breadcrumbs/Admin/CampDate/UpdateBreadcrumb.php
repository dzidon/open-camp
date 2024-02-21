<?php

namespace App\Service\Menu\Breadcrumbs\Admin\CampDate;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_camp_date_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_camp_date_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var CampDate $campDate */
        $campDate = $options['camp_date'];

        $this->addRoute($breadcrumbs, 'admin_camp_date_update', [
            'id' => $campDate->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_date');
        $resolver->setAllowedTypes('camp_date', CampDate::class);
        $resolver->setRequired('camp_date');
    }
}