<?php

namespace App\Service\Menu\Breadcrumbs\Admin\DownloadableFile;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\DownloadableFile;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_downloadable_file_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_downloadable_file_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var DownloadableFile $downloadableFile */
        $downloadableFile = $options['downloadable_file'];

        $this->addRoute($breadcrumbs, 'admin_downloadable_file_delete', [
            'id' => $downloadableFile->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('downloadable_file');
        $resolver->setAllowedTypes('downloadable_file', DownloadableFile::class);
        $resolver->setRequired('downloadable_file');
    }
}