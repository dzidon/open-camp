<?php

namespace App\Service\Menu\Breadcrumbs\Admin\CampImage;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampImage;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_camp_image_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_camp_image_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var CampImage $campImage */
        $campImage = $options['camp_image'];

        $this->addRoute($breadcrumbs, 'admin_camp_image_delete', [
            'id' => $campImage->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_image');
        $resolver->setAllowedTypes('camp_image', CampImage::class);
        $resolver->setRequired('camp_image');
    }
}