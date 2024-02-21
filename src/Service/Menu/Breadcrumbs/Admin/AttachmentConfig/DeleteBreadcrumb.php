<?php

namespace App\Service\Menu\Breadcrumbs\Admin\AttachmentConfig;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\AttachmentConfig;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_attachment_config_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_attachment_config_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var AttachmentConfig $attachmentConfig */
        $attachmentConfig = $options['attachment_config'];

        $this->addRoute($breadcrumbs, 'admin_attachment_config_delete', [
            'id' => $attachmentConfig->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('attachment_config');
        $resolver->setAllowedTypes('attachment_config', AttachmentConfig::class);
        $resolver->setRequired('attachment_config');
    }
}