<?php

namespace App\Service\Menu\Breadcrumbs\Admin\TripLocation;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\TripLocation;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_trip_location_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_trip_location_path_update';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var TripLocation $tripLocation */
        $tripLocation = $options['trip_location'];

        $this->addRoute($breadcrumbs, 'admin_trip_location_update', [
            'id' => $tripLocation->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('trip_location');
        $resolver->setAllowedTypes('trip_location', TripLocation::class);
        $resolver->setRequired('trip_location');
    }
}