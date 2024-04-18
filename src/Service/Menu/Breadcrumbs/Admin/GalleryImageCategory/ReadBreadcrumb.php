<?php

namespace App\Service\Menu\Breadcrumbs\Admin\GalleryImageCategory;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\GalleryImageCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_gallery_image_category_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_gallery_image_category_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var GalleryImageCategory $galleryImageCategory */
        $galleryImageCategory = $options['gallery_image_category'];

        $this->addRoute($breadcrumbs, 'admin_gallery_image_category_read', [
            'id' => $galleryImageCategory->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('gallery_image_category');
        $resolver->setAllowedTypes('gallery_image_category', GalleryImageCategory::class);
        $resolver->setRequired('gallery_image_category');
    }
}