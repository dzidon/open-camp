<?php

namespace App\Service\Menu\Breadcrumbs\User\CampCatalog;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_camp_catalog';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var null|CampCategory $campCategory */
        $campCategory = $options['camp_category'];

        $campCategories = [];
        $this->addRoute($breadcrumbs, 'user_camp_catalog');

        if ($campCategory !== null)
        {
            $campCategories = $campCategory->getAncestors();
            $campCategories[] = $campCategory;
        }

        foreach ($campCategories as $key => $campCategory)
        {
            $path = $campCategory->getPath();
            $text = $campCategory->getName();

            $this->addRoute($breadcrumbs, 'user_camp_catalog', ['path' => $path], 'user_camp_catalog_' . $key)
                ->setText($text)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_category');
        $resolver->setAllowedTypes('camp_category', ['null', CampCategory::class]);
        $resolver->setDefault('camp_category', null);
    }
}