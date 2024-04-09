<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationAdminAttachment;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\ApplicationAdminAttachment;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_admin_attachment_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_admin_attachment_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var ApplicationAdminAttachment $applicationAdminAttachment */
        $applicationAdminAttachment = $options['application_admin_attachment'];

        $this->addRoute($breadcrumbs, 'admin_application_admin_attachment_read', [
            'id' => $applicationAdminAttachment->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application_admin_attachment');
        $resolver->setAllowedTypes('application_admin_attachment', ApplicationAdminAttachment::class);
        $resolver->setRequired('application_admin_attachment');
    }
}