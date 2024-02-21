<?php

namespace App\Service\Menu\Breadcrumbs\Admin\CampCategory;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_camp_category_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_camp_category_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var CampCategory $campCategory */
        $campCategory = $options['camp_category'];

        $this->addRoute($breadcrumbs, 'admin_camp_category_update', [
            'id' => $campCategory->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_category');
        $resolver->setAllowedTypes('camp_category', CampCategory::class);
        $resolver->setRequired('camp_category');
    }
}