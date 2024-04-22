<?php

namespace App\Service\Menu\Breadcrumbs\User\GalleryImage;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\GalleryImageCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_gallery_image_list';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var null|GalleryImageCategory $galleryImageCategory */
        $galleryImageCategory = $options['gallery_image_category'];

        $campCategories = [];
        $this->addRoute($breadcrumbs, 'user_gallery_image_list');

        if ($galleryImageCategory !== null)
        {
            $campCategories = $galleryImageCategory->getAncestors();
            $campCategories[] = $galleryImageCategory;
        }

        foreach ($campCategories as $key => $galleryImageCategory)
        {
            $path = $galleryImageCategory->getPath();
            $text = $galleryImageCategory->getName();

            $this->addRoute($breadcrumbs, 'user_gallery_image_list', ['path' => $path], 'user_gallery_image_list_' . $key)
                ->setText($text)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('gallery_image_category');
        $resolver->setAllowedTypes('gallery_image_category', ['null', GalleryImageCategory::class]);
        $resolver->setDefault('gallery_image_category', null);
    }
}