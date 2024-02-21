<?php

namespace App\Service\Menu\Breadcrumbs\Admin\TripLocationPath;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\TripLocationPath;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_trip_location_path_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_trip_location_path_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var TripLocationPath $tripLocationPath */
        $tripLocationPath = $options['trip_location_path'];

        $this->addRoute($breadcrumbs, 'admin_trip_location_path_read', [
            'id' => $tripLocationPath->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('trip_location_path');
        $resolver->setAllowedTypes('trip_location_path', TripLocationPath::class);
        $resolver->setRequired('trip_location_path');
    }
}