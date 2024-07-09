<?php

namespace App\Service\Menu\Breadcrumbs\User\GalleryImage;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\GalleryImage;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_gallery_image_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_gallery_image_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var GalleryImage $galleryImage */
        $galleryImage = $options['gallery_image'];
        $id = $galleryImage->getId();

        $this->addRoute($breadcrumbs, 'user_gallery_image_read', ['galleryImageId' => $id]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('gallery_image');
        $resolver->setAllowedTypes('gallery_image', GalleryImage::class);
        $resolver->setRequired('gallery_image');
    }
}