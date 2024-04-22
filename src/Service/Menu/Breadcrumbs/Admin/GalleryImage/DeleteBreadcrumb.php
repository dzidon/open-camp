<?php

namespace App\Service\Menu\Breadcrumbs\Admin\GalleryImage;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\GalleryImage;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_gallery_image_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_gallery_image_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var GalleryImage $galleryImage */
        $galleryImage = $options['gallery_image'];

        $this->addRoute($breadcrumbs, 'admin_gallery_image_delete', [
            'id' => $galleryImage->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('gallery_image');
        $resolver->setAllowedTypes('gallery_image', GalleryImage::class);
        $resolver->setRequired('gallery_image');
    }
}